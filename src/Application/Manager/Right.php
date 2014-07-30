<?php
namespace Core\Application\Manager;

use Core\SystemMessages,
	App\Models\Right as RightModel,
	App\Models\User as UserModel,
	jamwork\common\Registry,
	jamwork\database\MysqlRecordset as Recordset;

/**
 * Right
 *
 * @category Core
 * @package  Core\Application\Manager
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class Right
{

	/**
	 * @param RightModel $right
	 * @return bool|\ReflectionClass
	 */
	public static function controllerExists(RightModel $right)
	{
		if ($right->getPrefix() == '')
		{
			$controller = '\\App\Modules\\' . ucfirst($right->getModule()) . '\\Controller\\' . ucfirst($right->getController());
		}
		else
		{
			$controller = '\\App\Modules\\' . ucfirst($right->getPrefix()) . '\\' . ucfirst($right->getModule()) . '\\Controller\\' . ucfirst($right->getController());
		}

		try
		{
			$reflection = new \ReflectionClass($controller);
		} catch (\Exception $e)
		{
			\Core\SystemMessages::addError($e->getMessage());

			return false;
		}

		return $reflection;
	}

	/**
	 * Zunächst wird das Recht aktualisiert und danach geprüft ob der Benutzer das Recht besitzt.
	 *
	 * @param RightModel $right Das zu prüfende Recht
	 * @param UserModel  $user  Der Benutzer
	 *
	 * @return bool
	 */
	public static function isAllowed(RightModel $right, UserModel $user)
	{
		if (!defined("UNITTEST"))
		{
			$reflection = self::controllerExists($right);

			if ($reflection === false)
			{
				return false;
			}

			$properties = $reflection->getDefaultProperties();

			if ($properties['checkPermissions'] == false || in_array($right->getAction(), $properties['noPermissionActions']))
			{
				return true;
			}
		}

		self::createRight($right);

		if ($user->getAdmin() || !defined('CHECK_PERMISSIONS') || (APPLICATION_ENV == ENV_DEV && CHECK_PERMISSIONS == false))
		{
			return true;
		}

		$con = Registry::getInstance()->getDatabase();

		$query = $con->newQuery()->select('1')->from('users as u')->innerJoin('right_group_users AS rgu')->on('rgu.user_id = u.id')->innerJoin('right_group_rights AS rgr')->on('rgr.group_id = rgu.group_id')->innerJoin('rights AS r')->on('r.id = rgr.right_id')->addWhere('u.id', $user->getId())->addWhere('r.module', $right->getModule())->addWhere('r.controller', $right->getController())->addWhere('r.action', $right->getAction())->addWhere('r.prefix', $right->getPrefix())->limit(0, 1);

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		return $rsExecution->isSuccessful() && $rsExecution->count() > 0;
	}

	/**
	 * @static
	 * @param string  $module
	 * @param  string $controller
	 * @param string  $action
	 * @param string  $prefix
	 * @param bool    $force
	 * @return string
	 */
	protected static function getActionName($module, $controller, $action, $prefix = '', $force = false)
	{
		$registry = Registry::getInstance();
		/**
		 * @var $sess    \jamwork\common\Session
		 */

		$toCheck = strtolower('getActionName:' . "$module:$controller:$action:$prefix");
		$sess = $registry->getSession();
		if (!$force && $sess->has($toCheck))
		{
			return $sess->get($toCheck);
		}
		$retArray = array();

		$prefixSlash = '';
		if (!empty($prefix))
		{
			$prefixSlash .= $prefix . "\\";
		}

		$class = "\\App\\Modules\\" . ucfirst($prefixSlash) . ucfirst($module) . "\\Controller\\" . ucfirst($controller);
		$reflect = new \ReflectionClass($class);

		$classDoc = $reflect->getDocComment();
		if ($classDoc !== false)
		{
			preg_match('/.*\@title([A-Za-z0-9äöüÄÖÜ\: \-\s\t]+).*/s', $classDoc, $matchClassDoc);
			if (!empty($matchClassDoc))
			{
				$retArray['title'] = trim($matchClassDoc[1]);
			}
			preg_match('/.*\@modulTitle([A-Za-z0-9äöüÄÖÜ\: \-\s\t]+).*/s', $classDoc, $matchClassDoc);
			if (!empty($matchClassDoc))
			{
				$retArray['modulTitle'] = trim($matchClassDoc[1]);
			}
			else{
				preg_match('/.*\@moduleTitle([A-Za-z0-9äöüÄÖÜ\: \-\s\t]+).*/s', $classDoc, $matchClassDoc);
				if (!empty($matchClassDoc))
				{
					$retArray['modulTitle'] = trim($matchClassDoc[1]);
				}
			}
		}

		//Methoden auslesen
		$methods = $reflect->getMethods();
		foreach ($methods as $method)
		{
			//Prüfe ob eine Methode eine HTML-Action ist
			preg_match("/(.+)(HTML|Html|JSON|Json)Action/", $method->getName(), $matches);
			if (!empty($matches))
			{
				//Lade den Kommentar
				$docComment = $method->getDocComment();
				$retArray['actionName'] = '';
				if ($docComment !== false)
				{
					//Hold den ActionName um in der Rechteverwaltung einen schönen titel zu haben
					//preg_match('/.*\@actionName ([A-Za-z0-9äöüÄÖÜ -\/]+).*$/s', $docComment, $matchDoc);
					preg_match('/.*\@actionName([A-Za-z0-9äöüÄÖÜ\: \/\-\s\t]+).*/s', $docComment, $matchDoc);


					if (!empty($matchDoc))
					{
						//Name des Aktion ermitteln

						$toCheck = strtolower('getActionName:' . "$module:$controller:" . $matches[1] . ":$prefix");
						$retArray['actionName'] = trim($matchDoc[1]);
						$sess->set($toCheck, $retArray);
					}
				}
			}
		}

		$toCheck = strtolower('getActionName:' . "$module:$controller:$action:$prefix");
		if ($sess->has($toCheck))
		{
			return $sess->get($toCheck);
		}

		return $retArray;
	}

	/**
	 * Erstellt ein neues Recht. Falls es schon existiert wird die "Modified"-Eigenschaft aktualisiert
	 *
	 * @param RightModel $right Rechte-Daten
	 * @param bool       $force
	 * @return bool
	 */
	public static function createRight($right, $force=false)
	{
		try
		{
			return self::createRightEx($right, $force);
		} catch (\Exception $e)
		{
			SystemMessages::addError($e->getMessage());
		}
	}

	/**
	 * @param      $right
	 * @param bool $force
	 * @return bool|mixed
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	protected static function createRightEx($right, $force=false)
	{
		$sql = "
			INSERT INTO
				rights
				set `module` = '%s',`controller` = '%s',`action` = '%s',`prefix` = '%s',`modified`= NOW(), `inactive`=0 %s
			ON DUPLICATE KEY UPDATE
				`modified` = NOW(), `inactive`=0 %s
		";

		if (is_array($right) && !empty($right))
		{
			$right = new RightModel($right);
		}

		// @actionName in der Action des Controllers "\App\Modules\Tinymce-extend\Controller\Parser" -> "test-tinymce" nicht gesetzt!

		if ($right instanceof RightModel)
		{

			/**
			 * prüfen ob bereits geprüft :-)
			 * @var $sess \jamwork\common\Session
			 */
			// echo '<pre>';
			// var_dump($right);
			// echo '</pre>';

			$toCheck = strtolower('setright' . $right->getModule() . ':' . $right->getController() . ':' . $right->getAction() . ':' . $right->getPrefix());
			$reg = Registry::getInstance();
			$sess = $reg->getSession();
			if (!$force && $sess->has($toCheck))
			{
				return true;
			}
			$sess->set($toCheck, 1);

			// und weiter gehts


			try
			{
				$actionInfo = self::getActionName($right->getModule(), $right->getController(), $right->getAction(), $right->getPrefix(), $force);
				$actionName = isset($actionInfo['actionName']) ? $actionInfo['actionName'] : '';
				$moduleTitle = isset($actionInfo['modulTitle']) ? $actionInfo['modulTitle'] : '';
				$controllerTitle = isset($actionInfo['title']) ? $actionInfo['title'] : '';
			} catch (\Exception $e)
			{
				$actionName = '';
			}

			if (empty($actionName))
			{
				if (APPLICATION_ENV == ENV_DEV && !defined("UNITTEST"))
				{
					$prefixSlash = '';
					$pre = $right->getPrefix();
					if (!empty($pre))
					{
						$prefixSlash .= $pre . "\\";
					}

					$class = "\\App\\Modules\\" . ucfirst($prefixSlash) . ucfirst($right->getModule()) . "\\Controller\\" . ucfirst($right->getController());

					throw new \Exception('@actionName in der Action des Controllers "' . $class . '" -> "' . $right->getAction() . '" nicht gesetzt!');
				}
			}
			$title = "";
			$modified = "";
			if (!empty($actionName))
			{
				$title = ", `title` = '$actionName'";
				$title .= ", `moduletitle` = '$moduleTitle'";
				$title .= ", `controllertitle` = '$controllerTitle'";
				$modified = ", `title` = '$actionName'";
				$modified .= ", `module` = '".lcfirst($right->getModule())."'";
				$modified .= ", `controller` = '".lcfirst($right->getController())."'";
				$modified .= ", `prefix` = '".lcfirst($right->getPrefix())."'";
				$modified .= ", `moduletitle` = '$moduleTitle'";
				$modified .= ", `controllertitle` = '$controllerTitle'";
			}
			/**
			 * mysql_real_escape_string wird hier nicht benötigt, Daten kommen bereits aus der Datenbank
			 *
			$queryString = sprintf(
				$sql,
				mysql_real_escape_string(lcfirst($right->getModule())),
				mysql_real_escape_string(lcfirst($right->getController())),
				mysql_real_escape_string($right->getAction()),
				mysql_real_escape_string(lcfirst($right->getPrefix())),
				$title,
				$modified
			);
			 */
			$queryString = sprintf(
				$sql,
				lcfirst($right->getModule()),
				lcfirst($right->getController()),
				$right->getAction(),
				lcfirst($right->getPrefix()),
				$title,
				$modified
			);

		}
		else
		{
			throw new \InvalidArgumentException('Invalid right definition');
		}

		$con = Registry::getInstance()->getDatabase();
		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($con->newQuery()->setQueryOnce($queryString));

		return $rsExecution->isSuccessful();
	}

	/**
	 * Liefert alle Rechte
	 *
	 * @return \Core\Application\Models\Right[]
	 */
	public static function getAllRights()
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()->select(
				'id,
				title,
				moduletitle,
				controllertitle,
				prefix,
				module,
				controller,
				action,
				inactive,
				modified'
		)->from('rights')->orderBy('prefix,module,controller,action ASC');

		$rights = array();
		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		if ($rsExecution->isSuccessfull())
		{
			while (($rs = $rsExecution->get()) == true)
			{
				$rights[] = new RightModel($rs);
			}
		}

		return $rights;
	}

	/**
	 * Liefert alle Rechte
	 *
	 * @param array $ids Array mit den IDs der zu liefernden Rechte
	 *
	 * @return \Core\Application\Models\Right[]
	 */
	public static function getRightsByMultipleIds(array $ids)
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()->select('id,
				title,
				module,
				controller,
				action,
				modified')->from('rights')->addWhere('id', $ids)->orderBy('module');

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		$rights = array();

		if ($rsExecution->isSuccessfull())
		{
			while (($rs = $rsExecution->get()) == true)
			{
				$rights[] = new RightModel($rs);
			}
		}

		return $rights;
	}

	/**
	 * Aktualisiert ein Recht
	 *
	 * @param RightModel $right Das zu aktualisierende Recht
	 *
	 * @return boolean
	 */
	public static function update(RightModel $right)
	{
		$datetime = new \DateTime();

		$con = Registry::getInstance()->getDatabase();

		return $con->update('rights', array(
		                                   'title' => $right->getTitle(),
		                                   'module' => $right->getModule(),
		                                   'controller' => $right->getController(),
		                                   'action' => $right->getAction(),
		                                   'prefix' => $right->getPrefix(),
		                                   'modified' => $datetime->format('Y-m-d H:i:s'),
		                                   'id' => $right->getId()
		                              ));
	}

	/**
	 * Liefert alle Rechte einer Rolle
	 *
	 * @param integer $groupId ID der zugehörigen Gruppe
	 *
	 * @return \Core\Application\Models\Right[]
	 */
	public static function getRightsByGroupId($groupId)
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()->select('r.id,
				r.title,
				r.module,
				r.controller,
				r.action,
				r.modified')->from('rights AS r')->innerJoin('right_group_rights AS rgr')->on('rgr.right_id = r.id')->addWhere('rgr.group_id', $groupId);

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		$rights = array();

		if ($rsExecution->isSuccessfull())
		{
			while (($rs = $rsExecution->get()) == true)
			{
				$rights[] = new RightModel($rs);
			}
		}

		return $rights;
	}
}
