<?php

namespace Core\Application\Manager;

use jamwork\common\Registry,
jamwork\database\Query,
jamwork\database\Update,
Core\SystemMessages,
Core\Model as BaseModel,
Core\Application\Interfaces\ModelsInterface;

class Base
{
	/**
	 * @var \jamwork\database\Database
	 */
	protected $con = null;


	/**
	 * @return void
	 */
	public function __construct()
	{
		$this->con = Registry::getInstance()->getDatabase();
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
			$inserted = $this->con->insert($model->getTableName(),$model->getDataRow());
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

		$updated = $this->con->update($model->getTableName(),$model->getDataRow());

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

		$deleted = $this->con->delete($model->getTableName(), $model->getDataRow());

		if (!$deleted)
		{
			SystemMessages::addError('Beim Entfernen ist ein Fehler aufgetreten');
		}

		$model->setId(0);

		return $deleted;
	}


	/**
	 * Liefert ein Model von ModelsInterface aus dem Query-Select
	 *
	 * @param \App\Models\ModelsInterface $model
	 * @param $id
	 * @return \App\Models\ModelsInterface
	 * @throws \ErrorException
	 */
	public function getModelById(ModelsInterface $model, $id)
	{
		$query = $this->con->newQuery();
		$query->select('*');
		$query->from($model->getTableName());
		$query->addWhere($model->getIdField(), $id);
		/**
		 * @var $rs \jamwork\database\MysqlRecordset
		 */
		$rs = $this->con->newRecordSet();
		$rs->execute($query);

		if ($rs->isSuccessful() && ($rs->count() > 0))
		{
			$model->setDataRow($rs->get());
			return $model;
		}

		$reflection = new \ReflectionClass($model);
		$name = $reflection->getName();

		throw new \ErrorException('Datensatz nicht gefunden mit ID "'.$id.'" in Model "'.$name.'"');
	}

	/**
	 * Liefert ein Array von Models aus dem Query-Select
	 *
	 * @param $modelClassName
	 * @param \jamwork\database\Query $query
	 * @return array
	 */
	public function getModelsByQuery($modelClassName, Query $query)
	{
		/**
		 * @var $rs \jamwork\database\MysqlRecordset
		 */
		$rs = $this->con->newRecordSet();
		$rs->execute($query);

		$models = array();

		if ($rs->isSuccessful() && ($rs->count() > 0)) {
			while (($rec = $rs->get()) == true) {
				$models[] = new $modelClassName($rec);
			}
		}

		return $models;
	}

	/**
	 * Liefert ein Model von $modelClassName aus dem Query-Select
	 *
	 * @param $modelClassName
	 * @param \jamwork\database\Query $query
	 * @return bool
	 */
	public function getModelByQuery($modelClassName, Query $query)
	{
		/**
		 * @var $rs \jamwork\database\MysqlRecordset
		 */
		$rs = $this->con->newRecordSet();
		$query->limit(0,1);
		$rs->execute($query);

		if ($rs->isSuccessful() && ($rs->count() > 0)) {
			return new $modelClassName($rs->get());
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
		 * @var $rs \jamwork\database\MysqlRecordset
		 */
		$rs = $this->con->newRecordSet();
		$rs->execute($query);

		$models = array();

		if ($rs->isSuccessful() && ($rs->count() > 0)) {
			while (($rec = $rs->get()) == true) {
				$models[] = $rec;
			}
		}
		return $models;
	}

	/**
	 * Führt ein Update anhand des übergebenen Update Objects aus.
	 *
	 * @param $update jamwork\database\Update
	 * @return bool
	 */
	public function updateByQuery(Query $query)
	{
		$rs = $this->con->newRecordSet();
		$ret = $rs->execute($query);

		return $ret->isSuccessful();
	}


}