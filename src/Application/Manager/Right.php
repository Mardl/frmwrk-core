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
	 * Zunächst wird das Recht aktualisiert und danach geprüft ob der Benutzer das Recht besitzt.
	 *
	 * @param RightModel $right Das zu prüfende Recht
	 * @param UserModel  $user  Der Benutzer
	 *
	 * @return bool
	 */
	public static function isAllowed(RightModel $right, UserModel $user)
	{
		self::createRight($right);

		if ($user->getAdmin() || !defined('CHECK_PERMISSIONS') || (APPLICATION_ENV < ENV_PROD && CHECK_PERMISSIONS == false))
		{
			return true;
		}

		$con = Registry::getInstance()->getDatabase();

		$query = $con->newQuery()->select('1')->from('users as u')->innerJoin('right_group_users AS rgu')->on('rgu.user_id = u.id')->innerJoin('right_group_rights AS rgr')->on('rgr.group_id = rgu.group_id')->innerJoin('rights AS r')->on('r.id = rgr.right_id')->addWhere('u.id', $user->getId())->addWhere('r.module', $right->getModule())->addWhere('r.controller', $right->getController())->addWhere('r.action', $right->getAction())->addWhere('r.prefix', $right->getPrefix())->limit(0, 1);

		/**
		 * @todo Return wert in Session besser über request->param halten für query, damit nicht jedesmal abgefragt wird.
		 */

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		return $rsExecution->isSuccessful() && $rsExecution->count() > 0;

		// alte version, wer fragt den returnwert ab ?? @mardl
		//return $rsExecution->get();
	}

	/**
	 * @static
	 * @param        $module
	 * @param        $controller
	 * @param        $action
	 * @param string $prefix
	 * @return string
	 */
	protected static function getActionName($module, $controller, $action, $prefix = '')
	{
		$registry = Registry::getInstance();
		/**
		 * @var $request \jamwork\common\HttpRequest
		 * @var $sess    \jamwork\common\Session
		 */
		$request = $registry->getRequest();

		$toCheck = strtolower('getActionName:' . "$module:$controller:$action:$prefix");
		$sess = $registry->getSession();
		if ($sess->has($toCheck))
		{
			return $sess->get($toCheck);
		}

		$prefixSlash = '';
		if (!empty($prefix))
		{
			$prefixSlash .= $prefix . "\\";
		}

		$class = "\\App\\Modules\\" . ucfirst($prefixSlash) . ucfirst($module) . "\\Controller\\" . ucfirst($controller);
		$reflect = new \ReflectionClass($class);

		//Methoden auslesen
		$methods = $reflect->getMethods();
		foreach ($methods as $method)
		{
			//Prüfe ob eine Methode eine HTML-Action ist
			preg_match("/(.+)(HTML|Html|JSON|Json)Action/", $method->getName(), $matches);
			if (!empty($matches))
			{
				// Initialisieren
				$request->setParameter("$module:$controller:" . strtolower($matches[1]) . ":$prefix", '');

				//Lade den Kommentar
				$docComment = $method->getDocComment();

				if ($docComment !== false)
				{
					//Prüfe ob im Kommentare der Tag showInNavigation vorhanden is und ob der Wert dann auch true ist
					preg_match('/.*\@actionName ([A-Za-z0-9äöüÄÖÜ -\/]+).*$/s', $docComment, $matchDoc);

					if (!empty($matchDoc))
					{
						//Name des Aktion ermitteln

						$toCheck = strtolower('getActionName:' . "$module:$controller:" . $matches[1] . ":$prefix");
						$sess->set($toCheck, $matchDoc[1]);
					}
				}
			}
		}

		$toCheck = strtolower('getActionName:' . "$module:$controller:$action:$prefix");
		if ($sess->has($toCheck))
		{
			return $sess->get($toCheck);
		}

		return '';
	}

	/**
	 * Erstellt ein neues Recht. Falls es schon existiert wird die "Modified"-Eigenschaft aktualisiert
	 *
	 * @param RightModel $right Rechte-Daten
	 * @throws \InvalidArgumentException Wenn die Rechte-Daten in einem umbekannten Format übergeben werden
	 * @return bool
	 */
	public static function createRight($right)
	{
		try
		{
			return self::createRightEx($right);
		} catch (\Exception $e)
		{
			SystemMessages::addError($e->getMessage());
		}
	}

	protected static function createRightEx($right)
	{
		$sql = "
			INSERT INTO
				rights
				set `module` = '%s',`controller` = '%s',`action` = '%s',`prefix` = '%s',`modified`= NOW() %s
			ON DUPLICATE KEY UPDATE
				`modified` = NOW() %s
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
			if ($sess->has($toCheck))
			{
				return true;
			}
			$sess->set($toCheck, 1);

			// und weiter gehts


			try
			{
				$actionName = self::getActionName($right->getModule(), $right->getController(), $right->getAction(), $right->getPrefix());
			} catch (\Exception $e)
			{
				$actionName = null;
			}

			if (empty($actionName))
			{
				if (APPLICATION_ENV < ENV_PROD && !defined("UNITTEST"))
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
			if (!empty($actionName))
			{
				$title = ", `title` = '$actionName'";
			}
			$queryString = sprintf($sql, mysql_real_escape_string($right->getModule()), mysql_real_escape_string($right->getController()), mysql_real_escape_string($right->getAction()), mysql_real_escape_string($right->getPrefix()), $title, $title);
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
		$query = $con->newQuery()->select('id,
				title,
				prefix,
				module,
				controller,
				action,
				modified')->from('rights')->orderBy('prefix,module,controller,action ASC');

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
