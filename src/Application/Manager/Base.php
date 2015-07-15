<?php

namespace Core\Application\Manager;

use jamwork\common\Registry;
use jamwork\database\Query;
use Core\SystemMessages;
use Core\Application\Interfaces\ModelsInterface;

/**
 * Class Base
 * @package Core\Application\Manager
 */
class Base
{

	/**
	 * @var \jamwork\database\PDODatabase|\jamwork\database\MssqlDatabase
	 */
	protected $con = null;


	/**
	 *
	 */
	public function __construct(\jamwork\database\Database $dataBase = null)
	{
		$this->con = is_null($dataBase) ? Registry::getInstance()->getDatabase() : $dataBase;
	}

	public function getConnection()
	{
		return $this->con;
	}

	/**
	 * Insert method
	 *
	 * @param ModelsInterface $model
	 *
	 * @return int|boolean
	 */
	public function insert(ModelsInterface $model)
	{
		$inserted = 0;

		if ($model->getId() == 0)
		{
			$model->setCreated();
			$model->setCreateduser_Id();
			$inserted = $this->con->insert($model->getTableName(), $model->getDataRow());
		}

		if (!$inserted)
		{
			SystemMessages::addError('Beim Erstellen ist ein Fehler aufgetreten');

			return false;
		}

		$model->setId($inserted);

		return $inserted;
	}

	/**
	 * Update method
	 *
	 * @param ModelsInterface $model
	 *
	 * @return int|boolean
	 */
	public function update(ModelsInterface $model)
	{
		if (!$model->getId())
		{
			return false;
		}

		$model->setModified();
		$model->setModifieduser_Id();
		$updated = $this->con->update($model->getTableName(), $model->getDataRow());

		if (!$updated)
		{
			SystemMessages::addError('Beim Aktualisieren ist ein Fehler aufgetreten');

			return false;
		}

		return $updated;
	}

	/**
	 * Delete method
	 *
	 * @param ModelsInterface $model
	 *
	 * @return boolean
	 */
	public function delete(ModelsInterface $model)
	{
		if (!$model->getId())
		{
			return false;
		}

		if (!$this->con->delete($model->getTableName(), $model->getDataRow()))
		{
			SystemMessages::addError('Beim Entfernen ist ein Fehler aufgetreten');

			return false;
		}

		$model->setId(0);

		return true;
	}

	/**
	 * @param string|ModelsInterface  $prefixOrModel
	 * @param \jamwork\database\Query $query
	 * @return \jamwork\database\Query
	 */
	public function addDeleteWhere($prefixOrModel, \jamwork\database\Query $query)
	{

		if ($prefixOrModel instanceof ModelsInterface)
		{
			if (property_exists($prefixOrModel, 'deleted'))
			{
				$query->addWhere($prefixOrModel->getTablePrefix() . 'deleted', 0);
			}
		}
		elseif (!is_object($prefixOrModel))
		{
			/**
			 * Wenn er hier her kommt, dann wu5rde die Funktion Public von außen aufgerufen
			 * Parameter als String übergeben. Somit weiß der Herr Programmierer, was er macht!
			 */
			$query->addWhere($prefixOrModel . 'deleted', 0);
		}

		return $query;
	}

	/**
	 * Liefert ein Model von ModelsInterface aus dem Query-Select
	 *
	 * @param \Core\Application\Interfaces\ModelsInterface $model
	 * @param                                              $id
	 * @return \Core\Application\Interfaces\ModelsInterface
	 * @throws \ErrorException
	 */
	public function getModelById(ModelsInterface $model, $id)
	{
		$query = $this->con->newQuery();
		$query->select('*');
		$query->from($model->getTableName());
		$query->addWhere($model->getIdField(), $id);
		$query = $this->addDeleteWhere($model, $query);

		/**
		 * @var $rs \jamwork\database\PDORecordset
		 */
		$rs = $this->con->newRecordSet();
		$rs->execute($query);

		if ($rs->isSuccessful() && ($rs->count() > 0))
		{
			$model->setDataRow($rs->get());
			$model->resetRegisterChange();


			return $model;
		}

		$reflection = new \ReflectionClass($model);
		$name = $reflection->getName();

		throw new \ErrorException('Datensatz nicht gefunden mit ID "' . $id . '" in Model "' . $name . '"');

	}

	/**
	 * Liefert ein Array von Models aus dem Query-Select
	 *
	 * @param string                  $modelClassName
	 * @param \jamwork\database\Query $query
	 * @return array
	 */
	public function getModelsByQuery($modelClassName, Query $query)
	{
		$query = $this->addDeleteWhere(new $modelClassName(), $query);
		/**
		 * @var $rs \jamwork\database\PDORecordset
		 */
		$rs = $this->con->newRecordSet();
		$rs->execute($query);

		$models = array();

		if ($rs->isSuccessful() && ($rs->count() > 0))
		{
			while (($rec = $rs->get()) == true)
			{
				/** @var $model \Core\Model */
				$model = new $modelClassName();
				$clean = $model->clearDataRow($rec);
				$model->setDataRow($clean);
				$model->resetRegisterChange();

				$models[] = $model;
			}
		}

		return $models;
	}

	/**
	 * Liefert ein Model von $modelClassName aus dem Query-Select
	 *
	 * @param string                  $modelClassName
	 * @param \jamwork\database\Query $query
	 * @return \Core\Application\Interfaces\ModelsInterface|bool
	 */
	public function getModelByQuery($modelClassName, Query $query)
	{
		$query = $this->addDeleteWhere(new $modelClassName(), $query);

		/**
		 * @var $rs \jamwork\database\PDORecordset
		 */
		$rs = $this->con->newRecordSet();
		$query->limit(0, 1);
		$rs->execute($query);

		if ($rs->isSuccessful() && ($rs->count() > 0))
		{
			/** @var $model \Core\Model */
			$model = new $modelClassName();
			$clean = $model->clearDataRow($rs->get());
			$model->setDataRow($clean);
			$model->resetRegisterChange();

			return $model;
		}

		return false;
	}

	/**
	 * Liefert ein Array von Records aus dem Query-Select
	 *
	 * @param \jamwork\database\Query $query
	 * @return array
	 */
	public function getArrayByQuery(Query $query)
	{
		/**
		 * @var $rs \jamwork\database\PDORecordset
		 */
		$rs = $this->con->newRecordSet();
		$rs->execute($query);

		$models = array();

		if ($rs->isSuccessful() && ($rs->count() > 0))
		{
			while (($rec = $rs->get()) == true)
			{
				$models[] = $rec;
			}
		}

		return $models;
	}

	/**
	 * Führt ein Update anhand des übergebenen Update Objects aus.
	 * @param \jamwork\database\Query $query
	 * @return bool
	 * @deprecated Wo bitte wird das verwendet? Da müsste der Update-Query ja per hand gebaut werden! Mardl
	 */
	public function updateByQuery(Query $query)
	{
		/**
		 * @var $ret \jamwork\database\PDORecordset
		 */
		$rs = $this->con->newRecordSet();
		$ret = $rs->execute($query);

		return $ret->isSuccessful();
	}


}