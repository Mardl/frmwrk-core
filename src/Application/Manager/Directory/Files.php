<?php


namespace Core\Application\Manager\Directory;

use Core\Application\Models\Directory\Files as FilesModel,
	Core\Application\Manager\Directory\Files as FilesManager,
	Core\Application\Manager\Directory as DirectoryManager,
	jamwork\common\Registry,
	Core\SystemMessages,
	App\Manager\User as UserManager;

/**
 * Class Files
 *
 * @category Manager
 * @package  Manager
 * @author   Reinhard Hampl <reini@dreiwerken.de>
 */
class Files
{

	/**
	 * @var array MIME-Types
	 */
	private static $mimetypes = array(
		"gif" => "image/gif",
		"jfif" => "image/pipeg",
		"jpe" => "image/jpeg",
		"jpeg" => "image/jpeg",
		"jpg" => "image/jpeg",
		"mov" => "video/quicktime",
		"mp4" => "video/mp4",
		"pdf" => "application/pdf",
		"png" => "image/png",
		"svg" => "image/svg+xml",
		"webm" => "video/webm",
		"ogg" => "video/ogg",
		"ogv" => "video/ogg"
	);

	private static $cache = array();

	/**
	 * Liefert eine Datei anhand seiner ID
	 *
	 * @param int $fileId ID der Datei
	 *
	 * @return \Core\Application\Models\Directory\Files
	 *
	 * @throws \ErrorException Wenn die Datei nicht gefunden wurde
	 * @throws \InvalidArgumentException Wenn eine leere Filesid übermittelwurde oder keine DirectorysId hinterlegt ist
	 */
	public static function getFileById($fileId)
	{
		if (empty($fileId))
		{
			throw new \InvalidArgumentException('Invalid File ID');
		}

		if (array_key_exists($fileId, self::$cache))
		{
			return self::$cache[$fileId];
		}

		$con = Registry::getInstance()->getDatabase();

		$query = $con->newQuery()->select('id, orgname, name, size, basename, directory_id as directory, parent_id as parent, mimetype')->from('files')->addWhere('id', $fileId)->limit(0, 1);

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			$rs = $rsExecution->get();

			$rs['directory'] = DirectoryManager::getDirectoryById($rs['directory']);

			if (!is_null($rs['parent']))
			{
				$rs['parent'] = self::getFileById($rs['parent']);
			}

			$file = new FilesModel($rs);
			$files[] = $file;

			self::$cache[$fileId] = $file;

			return $file;
		}
		throw new \ErrorException('Datei nicht gefunden!');
	}

	/**
	 * Liefert ein Array von Files abhängig von der übermittelten Directory Id
	 *
	 * @param int $directoryId directoryId
	 * @return \Core\Application\Models\Directory\Files[]
	 */
	public static function getFilesByDirectoryId($directoryId)
	{
		$con = Registry::getInstance()->getDatabase();

		$query = $con->newQuery()->select('id, orgname, name, size, basename, directory_id as directory, parent_id as parent, mimetype')->from('files')->addWhere('directory_id', $directoryId)->addWhereIsNull('parent_id');

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		$files = array();

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			while (($rs = $rsExecution->get()) == true)
			{
				//$directory = array_pop($rs);
				$rs['directory'] = DirectoryManager::getDirectoryById($rs['directory']);

				if (!is_null($rs['parent']))
				{
					$rs['parent'] = self::getFileById($rs['parent']);
				}

				$file = new FilesModel($rs);

				//$file->setDirectory(DirectoryManager::getDirectoryById($directory));
				$files[] = $file;
			}
		}

		return $files;
	}

	/**
	 * Speichert eine neue Datei auf dem System aus dem filePost. Dabei überprüft die Funktion,
	 * ob der Eintrag bereits vorhanden ist und somit geändert werden muss oder ob es sich um eine neue Datei handelt.
	 *
	 * @param array      $filePost
	 * @param int        $directoryId
	 * @param FilesModel $filemodel
	 * @param bool       $addSource
	 * @param int        $watermark
	 *
	 * @return bool|\Core\Application\Models\Directory\Files
	 *
	 * @throws \ErrorException
	 */
	public static function saveUploadedFile($filePost, $directoryId, FilesModel $filemodel = null, $addSource = false, $watermark = 0)
	{
		$originalName = $filePost['name'];
		$tmpName = $filePost['tmp_name'];
		$filesize = $filePost['size'];
		$isNew = $filemodel == null;

		$fileinfo = pathinfo($originalName);
		$extension = $fileinfo['extension'];

		$newFileName = md5($originalName . time()) . '.' . $extension;
		$newName = FILE_PATH . $newFileName;

		if (!file_exists(FILE_PATH))
		{
			mkdir(FILE_PATH);
			@chmod(FILE_PATH, 0777);
		}

		if (move_uploaded_file($tmpName, $newName))
		{
			$info = exif_read_data($newName);

			if (isset($info["Orientation"]) && $info["Orientation"] != "1")
			{
				/*
				1 = The 0th row is at the visual top of the image, and the 0th column is the visual left-hand side.
				2 = The 0th row is at the visual top of the image, and the 0th column is the visual right-hand side.
				3 = The 0th row is at the visual bottom of the image, and the 0th column is the visual right-hand side.
				4 = The 0th row is at the visual bottom of the image, and the 0th column is the visual left-hand side.
				5 = The 0th row is the visual left-hand side of the image, and the 0th column is the visual top.
				6 = The 0th row is the visual right-hand side of the image, and the 0th column is the visual top.
				7 = The 0th row is the visual right-hand side of the image, and the 0th column is the visual bottom.
				8 = The 0th row is the visual left-hand side of the image, and the 0th column is the visual bottom.

				http://sylvana.net/jpegcrop/exif_orientation.html
				 */

				$flip = false;
				$degree = false;

				switch ($info["Orientation"])
				{
					case 2: //flip horizontal
						$flip = "flopImage";
						$doit = false;
						break;
					case 3: //rotate 180
						$degree = 180;
						$doit = true;
						break;
					case 4: //flip vertical
						$flip = "flipImage";
						$doit = false;
						break;
					case 5: //rotate 90, flip horizontal
						$degree = 90;
						$flip = "flopImage";
						$doit = true;
						break;
					case 6: //rotate 90
						$degree = 90;
						$doit = true;
						break;
					case 7: //rotate -90, flip horizontal
						$degree = -90;
						$flip = "flopImage";
						$doit = false;
						break;
					case 8: //rotate -90
						$degree = -90;
						$doit = true;
						break;
					default:
						$doit = false;
						break;
				}

				if ($doit)
				{
					$imagick = new \Imagick();
					$imagick->readImage($newName);
					if ($degree)
					{
						$imagick->rotateImage(new \ImagickPixel('none'), $degree);
					}
					if ($flip)
					{
						$imagick->$flip();
					}
					$imagick->writeImage($newName);
					$imagick->clear();
					$imagick->destroy();
				}
			}

			if ($watermark != 0)
			{
				$pWatermark = ROOT_PATH . "/html/static/images/watermark.png";
				if (file_exists($pWatermark))
				{
					$pFiles = ROOT_PATH . "/html/" . $newName;
					$sizesF = getimagesize($pFiles);
					$sizesW = getimagesize($pWatermark);

					$x = ($sizesF[0] / 2) - ($sizesW[0] / 2);
					$y = ($sizesF[1] / 2) - ($sizesW[1] / 2);

					$watermarking = "composite -geometry +" . $x . "+" . $y . " " . $pWatermark . " " . $pFiles . " " . $pFiles;
					system($watermarking);
				}
			}

			if ($isNew)
			{
				$filemodel = new FilesModel();
			}
			else //Edit Modus -> alte Datei löschen
			{
				if (file_exists(FILE_PATH . $filemodel->getName()) && $addSource == false)
				{
					unlink(FILE_PATH . $filemodel->getName());
				}
			}

			$directoryModel = DirectoryManager::getDirectoryById($directoryId);

			$filemodel->setOrgname($originalName);
			$filemodel->setName($newFileName);
			$filemodel->setDirectory($directoryModel);
			$filemodel->setSize(($filesize / 1024)); //umrechnen in KB

			if (!array_key_exists($extension, self::$mimetypes))
			{
				throw new \ErrorException('Nicht unterstützter Dateityp!');
			}
			$mimetype = self::$mimetypes[$extension];

			$filemodel->setMimetype($mimetype);

			if ($isNew || $addSource == true)
			{
				self::insertFile($filemodel);
			}
			else
			{
				self::updateFile($filemodel);
			}

			return $filemodel;
		}

		return false;
	}

	/**
	 * @param string $filename Dateiname
	 * @return bool|\Core\Application\Models\Directory\Files
	 * @throws \InvalidArgumentException
	 */
	public static function fileExistsByName($filename)
	{
		if (empty($filename))
		{
			throw new \InvalidArgumentException('Ungültige Datei ID!');
		}

		$con = Registry::getInstance()->getDatabase();

		$query = $con->newQuery()->select('id, orgname, name, size, basename, directory_id')->from('files')->addWhere('name', $filename)->limit(0, 1);

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			$rs = $rsExecution->get();

			$directory = array_pop($rs);
			$file = new FilesModel($rs);

			$file->setDirectory(DirectoryManager::getDirectoryById($directory));

			return $file;
		}

		return false;
	}

	public static function saveProfileFile(FilesModel $filesModel, $lifeId)
	{
		$exists = self::fileExistsByName($filesModel->getName());
		if ($exists === false)
		{
			if (self::insertFile($filesModel) !== false)
			{
				$user = UserManager::getUserById($lifeId);
				//$user->setAvatar($filesModel->getThumbnailTarget());
				$user->setAvatar($filesModel->getId());
			}
		}
		else
		{
			self::clearCache();
			// funktioniert noch nicht
			// $cachefile = FILE_TEMP.md5($lifeId).'*';
			// @unlink($cachefile);

			$user = UserManager::getUserById($lifeId);
			//$user->setAvatar($exists->getThumbnailTarget());
			$user->setAvatar($exists->getId());
		}

		UserManager::updateUser($user);
	}

	/**
	 * Löscht eine Datei aus der Datenbank sowie physikalisch vom Server
	 *
	 * @param int $fileId ID der Datei
	 *
	 * @return bool
	 */
	public static function deleteUploadedFile($fileId)
	{
		unset(self::$cache[$fileId]);

		$bRet = true;

		$fileModel = FilesManager::getFileById($fileId);
		if (file_exists(FILE_PATH . $fileModel->getName()))
		{
			$bRet = $bRet && unlink(FILE_PATH . $fileModel->getName());
		}
		$bRet = $bRet && self::deleteFile($fileModel->getId());

		return $bRet;
	}

	/**
	 * Fügt einen neuen Eintrag in der Datei Tabelle ein
	 *
	 * @param \Core\Application\Models\Directory\Files $filemodel FileModel, das in die DB gespeichert werden soll
	 *
	 * @return bool|\Core\Application\Models\Directory\Files
	 */
	private function insertFile(FilesModel $filemodel)
	{
		$con = Registry::getInstance()->getDatabase();
		$info = pathinfo($filemodel->getName());

		$query = sprintf("INSERT INTO
				files (
				directory_id,
				orgname,
				name,
				size,
				basename,
				parent_id,
				mimetype
				)
			VALUES
				(%d, '%s', '%s', %d, '%s', %s, '%s');", mysql_real_escape_string($filemodel->getDirectory()->getId()), mysql_real_escape_string($filemodel->getOrgname()), mysql_real_escape_string($filemodel->getName()), mysql_real_escape_string($filemodel->getSize()), mysql_real_escape_string(basename($filemodel->getName(), '.' . @$info['extension'])), mysql_real_escape_string(($filemodel->getParent()) ? $filemodel->getParent()->getId() : 'NULL'), mysql_real_escape_string($filemodel->getMimetype()));

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($con->newQuery()->setQueryOnce($query));

		if (!$rsExecution->isSuccessfull())
		{
			SystemMessages::addError('Beim Speichern der Datei ist ein Fehler aufgetreten!');

			return false;
		}

		$filemodel->setId(mysql_insert_id());

		return $filemodel;
	}

	/**
	 * Löscht die Datei
	 *
	 * @param int $filesId ID der Datei
	 * @return bool
	 */
	private function deleteFile($filesId)
	{
		unset(self::$cache[$filesId]);

		$con = Registry::getInstance()->getDatabase();
		//Delete Sources
		$query = sprintf("DELETE FROM
				files
			WHERE parent_id = %d;", mysql_real_escape_string($filesId));
		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($con->newQuery()->setQueryOnce($query));

		// Verknpüfungen löschen
		$query = sprintf("UPDATE nutritioncategory SET file_id = NULL WHERE file_id = %d;", mysql_real_escape_string($filesId));
		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($con->newQuery()->setQueryOnce($query));

		$query = sprintf("UPDATE object_nutritions SET file_id = NULL WHERE file_id = %d;", mysql_real_escape_string($filesId));
		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($con->newQuery()->setQueryOnce($query));

		$query = sprintf("UPDATE object_questionaries SET file_id = NULL WHERE file_id = %d;", mysql_real_escape_string($filesId));
		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($con->newQuery()->setQueryOnce($query));

		$query = sprintf("UPDATE object_questions SET file_id = NULL WHERE file_id = %d;", mysql_real_escape_string($filesId));
		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($con->newQuery()->setQueryOnce($query));

		$query = sprintf("UPDATE object_nutritions SET file_id = NULL WHERE file_id = %d;", mysql_real_escape_string($filesId));
		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($con->newQuery()->setQueryOnce($query));

		$query = sprintf("UPDATE object_rezept SET file_id = NULL WHERE file_id = %d;", mysql_real_escape_string($filesId));
		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($con->newQuery()->setQueryOnce($query));

		$query = sprintf("UPDATE object_textelements SET file_id = NULL WHERE file_id = %d;", mysql_real_escape_string($filesId));
		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($con->newQuery()->setQueryOnce($query));

		// Delete file
		$query = sprintf("DELETE FROM
				files
			WHERE id = %d;", mysql_real_escape_string($filesId));
		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($con->newQuery()->setQueryOnce($query));

		if (!$rsExecution->isSuccessfull() || mysql_affected_rows() == 0)
		{
			SystemMessages::addError('Fehler beim Löschen der Datei!');

			return false;
		}

		return true;
	}

	/**
	 * Aktualisiert den File Eintrag in der Datenbank
	 *
	 * @param FilesModel $fileModel File Model der zu aktualisierenden Datei
	 *
	 * @return \Core\Application\Models\Directory\Files|bool
	 */
	public static function updateFile(FilesModel $fileModel)
	{
		unset(self::$cache[$fileModel->getId()]);

		$con = Registry::getInstance()->getDatabase();

		$info = pathinfo($fileModel->getName());

		$query = sprintf("UPDATE
				files
			SET
				directory_id = %d,
				orgname = '%s',
				name = '%s',
				size = %d,
				basename = '%s',
				mimetype = '%s'
			WHERE
				id = %d;", mysql_real_escape_string($fileModel->getDirectory()->getId()), mysql_real_escape_string($fileModel->getOrgname()), mysql_real_escape_string($fileModel->getName()), mysql_real_escape_string($fileModel->getSize()), mysql_real_escape_string(basename($fileModel->getName(), '.' . $info['extension'])), mysql_real_escape_string($fileModel->getMimetype()), mysql_real_escape_string($fileModel->getId()));

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($con->newQuery()->setQueryOnce($query));

		if (!$rsExecution->isSuccessfull())
		{
			SystemMessages::addError('Fehler beim aktualisieren der Datei!');

			return false;
		}

		return $fileModel;
	}

	/**
	 * Prüft, ob es sich bei der Datei um ein Image handelt
	 *
	 * @param \Core\Application\Models\Directory\Files $file FileModel der Datei, das überprüft werden soll
	 *
	 * @return bool|int
	 */
	public function isImage(FilesModel $file)
	{
		if ($file->isEmpty())
		{
			return false;
		}

		return exif_imagetype(FILE_PATH . $file->getName());
	}

	/**
	 * Leert das Cache Verzeichnis der Dateien
	 *
	 * @return bool
	 */
	public function clearCache()
	{
		$bRet = true;

		$directory = dir(FILE_TEMP);

		while (false !== ($entry = $directory->read()))
		{
			if ($entry != "." && $entry != "..")
			{
				$bRet = $bRet && unlink(FILE_TEMP . $entry);
			}
		}

		return $bRet;
	}

	/**
	 * Konvertiert das übergebene Bild in die angegebene Größe und speichert es im Cache
	 * Verzeichnis (mit Angabe der Größe im Dateinamen). Liefert den Dateipfad der neuen Datei.
	 *
	 * @param \Core\Application\Models\Directory\Files $file   FileModel des betroffenen Bildes
	 * @param int                                      $width  Breite des Bildes
	 * @param int                                      $height Höhe des Bildes
	 *
	 * @return string
	 */
	public function getThumbnail(FilesModel $file, $width = 0, $height = 0)
	{
		if ($file->getId() == 0)
		{
			return '';
		}

		if (empty($width) && empty($height))
		{
			return FILE_PATH . $file->getName();
		}

		$source = ROOT_PATH . '/html/' . FILE_PATH . $file->getName();

		$colorspace = self::getColorspace($source);
		$width = empty($width) ? '' : $width;
		$height = empty($height) ? '' : $height;
		$resize = '';
		$density = '-density 72';
		$quality = '-quality 82';
		$target = self::getTarget($file->getName(), $width, $height);

		if (file_exists(ROOT_PATH . '/html/' . $target))
		{
			return '/' . $target;
		}

		if (!file_exists(ROOT_PATH . '/html/' . FILE_TEMP))
		{
			mkdir(FILE_TEMP);
			@chmod(FILE_TEMP, 0777);
		}

		if ($width > 0 || $height > 0)
		{
			$resize = '-resize "' . $width . 'x' . $height . '"';
		}

		$imageConvert = 'convert "' . $source . '" ' . $colorspace . ' ';
		if (!empty($resize))
		{
			$imageConvert .= $resize . ' ';
		}

		$imageConvert .= $density . ' ' . $quality . ' -antialias ';
		$imageConvert .= '-strip';
		$imageConvert .= ' "' . ROOT_PATH . '/html/' . $target . '"';

		system($imageConvert);

		if (!empty($this->target))
		{
			@chmod($this->target, 0777);
		}

		return '/' . $target;
	}

	/**
	 * Liefert den neuen Dateinamen mit Pfad
	 *
	 * @param string $filename Original-Dateiname
	 * @param int    $width    Breite der Datei
	 * @param int    $height   Höhe der Datei
	 *
	 * @return string
	 */
	private function getTarget($filename, $width, $height)
	{
		$fileinfo = pathinfo($filename);
		$extension = $fileinfo['extension'];

		$filename = str_replace('.' . $extension, '', $filename);
		$filename .= '_' . $width . 'x' . $height . '.' . $extension;

		return FILE_TEMP . $filename;
	}

	/**
	 * Liefert den Colorspace
	 *
	 * @param string $filename Dateiname mit Pfad
	 * @return string
	 */
	private function getColorspace($filename)
	{
		$fileinfo = pathinfo($filename);
		$extension = $fileinfo['extension'];

		switch (strtolower($extension))
		{
			case "jpeg":
			case "jpg":
				return "-colorspace RGB -type TrueColor";
				break;
			case "gif":
				return "-colors 256";
				break;

			case "png":
				return "-colorspace RGB";
				break;
		}

		return '';
	}

	/**
	 * @param \Core\Application\Models\Directory\Files $filemodel
	 * @return array
	 */
	public function getSourcesByModel(FilesModel $filemodel)
	{
		$sources = array();

		$sources[] = array($filemodel->getName(), $filemodel->getMimetype());

		$con = Registry::getInstance()->getDatabase();

		$query = $con->newQuery()->select('name, mimetype')->from('files')->addWhere('parent_id', $filemodel->getId());

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			while (($rs = $rsExecution->get()) == true)
			{
				$sources[] = array($rs['name'], $rs['mimetype']);
			}
		}

		return $sources;
	}

	/**
	 * Liefert Datei zurück anhand des Suchbegriffs
	 *
	 * @param string $searchTerm Suchbegriff
	 * @return array
	 */
	public static function getFilesByLikeName($searchTerm)
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery();

		$query->select('id, orgname, name')->from('files')->addWhereLike('orgname', $searchTerm);

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);
		$dataArray = array();

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			while (($rs = $rsExecution->get()) == true)
			{
				$dataArray[] = array(
					'id' => $rs['id'],
					'filename' => $rs['name'],
					'value' => $rs['orgname'],
					'label' => $rs['orgname']
				);
			}
		}

		return $dataArray;
	}
}
