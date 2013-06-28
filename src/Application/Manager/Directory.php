<?php

namespace Core\Application\Manager;

use jamwork\debug\DebugLogger,
	Core\Application\Models\Directory as DirectoryModel,
	Core\Application\Manager\Directory\Files as FilesManager,
	jamwork\common\Registry,
	Core\SystemMessages,
	jamwork\database\MysqlRecordset as Recordset;

/**
 * Class Directory
 *
 * @category Core
 * @package  Core\Application\Manager
 * @author   Reinhard Hampl <reini@dreiwerken.de>
 */
class Directory
{

	/**
	 * @var array
	 */
	private static $cache = array();

	/**
	 * Liefert ein Directory anhand seiner Id
	 *
	 * @param int $directoryId Id des gewünschten Directory
	 * @throws \InvalidArgumentException Wenn eine leere Directoryid übermittelt wurde
	 * @throws \ErrorException Wenn das gewünschte Directory nicht gefunden wurde
	 * @return DirectoryModel
	 */
	public static function getDirectoryById($directoryId)
	{
		if (empty($directoryId))
		{
			throw new \InvalidArgumentException('Ungültige Verzeichnis ID!');
		}

		if (array_key_exists($directoryId, self::$cache))
		{
			return self::$cache[$directoryId];
		}

		$con = Registry::getInstance()->getDatabase();

		$query = $con->newQuery()->select('id, name, sort, parent_id')->from('directories')->addWhere('id', $directoryId)->limit(0, 1);

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			$rs = $rsExecution->get();

			$parentDirectory = array_pop($rs);
			$directory = new DirectoryModel($rs);
			if (!empty($parentDirectory))
			{
				$directory->setParentDirectory(self::getDirectoryById($parentDirectory));
			}

			self::$cache[$directoryId] = $directory;

			return $directory;
		}

		throw new \ErrorException('Verzeichnis nicht gefunden!');
	}

	/**
	 * Liefert ein Array von Directories anhängig von der Parent Directory ID
	 *
	 * @param integer $parentDirectoryId gewünschtes Parent Directory
	 *
	 * @return array
	 */
	public static function getDirectoriesByParentId($parentDirectoryId)
	{
		$con = Registry::getInstance()->getDatabase();

		if ($parentDirectoryId == 0)
		{
			$query = $con->newQuery()->select('id, name, sort, parent_id')->from('directories')->addWhereIsNull('parent_id')->orderby('sort');
		}
		else
		{
			$query = $con->newQuery()->select('id, name, sort, parent_id')->from('directories')->addWhere('parent_id', $parentDirectoryId)->orderby('sort');
		}

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		$directories = array();

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			while (($rs = $rsExecution->get()) == true)
			{
				$parentDirectory = array_pop($rs);
				$directory = new DirectoryModel($rs);
				if (!empty($parentDirectory))
				{
					$directory->setParentDirectory(self::getDirectoryById($parentDirectory));
				}

				$directories[] = $directory;
			}
		}

		return $directories;
	}

	/**
	 * Liefert alle Childelemente des Directories (Directories und Files)
	 *
	 * @param int $idDirectory Id von dem die Children benötigt werden
	 * @return array mit App\Models\Directory\Files und App\Models\Directory
	 */
	public static function getChildren($idDirectory)
	{
		return self::getDirectoriesByParentId($idDirectory);
	}

	/**
	 * Liefert alle Files des Directory
	 *
	 * @param int $idDirectory Id von dem die Files benötigt werden
	 * @return array
	 */
	public static function getChildrenFiles($idDirectory)
	{
		return FilesManager::getFilesByDirectoryId($idDirectory);
	}

	/**
	 * Speichert ein neues Verzeichnis in der Datenbank
	 *
	 * @static
	 * @param \Core\Application\Models\Directory $dirModel
	 * @return bool|\Core\Application\Models\Directory
	 */
	public static function insertDirectory(DirectoryModel $dirModel)
	{
		$con = Registry::getInstance()->getDatabase();
		$parentdirModel = $dirModel->getParentDirectory();

		if (empty($parentdirModel))
		{
			$query = sprintf("INSERT INTO
				directories (
				`parent_id`,
				`name`,
				`sort`
			)
					VALUES
					((null), '%s', %d);", mysql_real_escape_string($dirModel->getName()), mysql_real_escape_string($dirModel->getSort()));


		}
		else
		{
			$query = sprintf("INSERT INTO
				directories (
				`parent_id`,
				`name`,
				`sort`
			)
				VALUES
				(%d, '%s', %d);", mysql_real_escape_string($parentdirModel->getId()), mysql_real_escape_string($dirModel->getName()), mysql_real_escape_string($dirModel->getSort()));
			$parentId = $parentdirModel->getId();
		}

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($con->newQuery()->setQueryOnce($query));

		if (!$rsExecution->isSuccessfull())
		{
			SystemMessages::addError('Beim Erstellen des Verzeichnisses ist ein Fehler aufgetreten!');

			return false;
		}

		$dirModel->setId(mysql_insert_id());

		return $dirModel;
	}

	/**
	 * Aktualisiert den Directories Eintrag, liefert true wenn erfolgreich ansonsten false
	 *
	 * @param DirectoryModel $dirModel Directory Model das aktualisiert werden soll
	 *
	 * @return bool
	 */
	public static function updateDirectory(DirectoryModel $dirModel)
	{
		unset(self::$cache[$dirModel->getId()]);

		$con = Registry::getInstance()->getDatabase();
		$parentdirModel = $dirModel->getParentDirectory();

		if (empty($parentdirModel))
		{
			$query = sprintf("UPDATE
					directories
				SET
					parent_id = (null),
					name = '%s',
					sort = %d
				WHERE
					id = %d
				;", mysql_real_escape_string($dirModel->getName()), mysql_real_escape_string($dirModel->getSort()), mysql_real_escape_string($dirModel->getId()));
		}
		else
		{
			$query = sprintf("UPDATE
					directories
				SET
					parent_id = %d,
					name = '%s',
					sort = %d
				WHERE
					id = %d
				;", mysql_real_escape_string($dirModel->getParentDirectory()->getId()), mysql_real_escape_string($dirModel->getName()), mysql_real_escape_string($dirModel->getSort()), mysql_real_escape_string($dirModel->getId()));
		}

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($con->newQuery()->setQueryOnce($query));

		if (!$rsExecution->isSuccessfull())
		{
			SystemMessages::addError('Beim Aktualisieren des Verzeichnisses ist ein Fehler aufgetreten!');

			return false;
		}

		return true;
	}

	/**
	 * Löscht das gewünschte Verzeichnis
	 *
	 * @static
	 * @param int $directoryId ID des Verzeichnisses, das gelöscht werden soll
	 * @return bool
	 */
	public static function deleteDirectory($directoryId)
	{
		unset(self::$cache[$directoryId]);

		if ($directoryId == 0)
		{
			SystemMessages::addError('Es wurde keine Verzeichnis ID übergeben!');

			return false;
		}

		$con = Registry::getInstance()->getDatabase();

		$query = sprintf("DELETE FROM
				directories
			WHERE
				id = %d
			;", mysql_real_escape_string($directoryId));

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($con->newQuery()->setQueryOnce($query));

		if ($rsExecution->isSuccessfull() && mysql_affected_rows() == 0)
		{
			SystemMessages::addError('Beim Löschen des Verzeichnisses ist ein Fehler aufgetreten!');

			return false;
		}

		if (!$rsExecution->isSuccessfull())
		{
			SystemMessages::addError('Verzeichnis kann nicht gelöscht werden, da noch Unterelemente existieren!');

			return false;
		}

		return true;
	}

	/**
	 * Liefert alle Einträge aus Tabelle directories im json-format
	 *
	 * @param string $searchString Suchphrase
	 *
	 * @throws \ErrorException Wenn keine Daten zurückgegeben wurden
	 *
	 * @return array
	 */
	public static function getDirectorysAsJson($searchString)
	{
		$con = Registry::getInstance()->getDatabase();

		$query = $con->newQuery()->select('id, name as value, parent_id')->from('directories')->where("name like '" . mysql_real_escape_string($searchString) . "%'")->orderby('sort');

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		$directoriesJson = array();

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{

			while (($rs = $rsExecution->get()) == true)
			{
				$directoriesJson[] = $rs;
			}

			$directoriesJson = json_encode($directoriesJson);

			return $directoriesJson;
		}

		throw new \ErrorException('Verzeichnis nicht gefunden!');
	}

	/**
	 * Liefert ein Directory anhand seiner Bezeichnung
	 *
	 * @param string $directory Bezeichnung
	 *
	 * @return DirectoryModel
	 *
	 * @throws \ErrorException Wenn das gewünschte Directory nicht gefunden wurde
	 * @throws \InvalidArgumentException Wenn eine leere Bezeichnung übermittelt wurde
	 */
	public static function getDirectoryByTitle($directory)
	{
		if (empty($directory))
		{
			throw new \InvalidArgumentException('Ungültiger Titel des Verzeichnisses!');
		}

		$con = Registry::getInstance()->getDatabase();

		$query = $con->newQuery()->select('id, name, sort, parent_id as parent')->from('directories')->addWhere('name', $directory)->limit(0, 1);

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			$rs = $rsExecution->get();

			if (!empty($rs['parent']))
			{
				$rs['parent'] = self::getDirectoryById($rs['parent']);
			}


			$directory = new DirectoryModel($rs);

			return $directory;
		}

		throw new \ErrorException('Verzeichnis nicht gefunden!');
	}
}
