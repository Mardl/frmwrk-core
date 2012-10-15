<?php
/**
 * Rechte Manager
 *
 * PHP version 5.3
 *
 * @category Manager
 * @package  Manager
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace Core\Application\Manager;

use Core\Application\Models\Right as RightModel,
	Core\Application\Models\User as UserModel,
	jamwork\common\Registry,
	jamwork\database\MysqlRecordset as Recordset;

/**
 * Right
 *
 * @category Manager
 * @package  Manager
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class Right
{

	/**
	 * Zunächst wird das Recht aktualisiert und danach geprüft ob der Benutzer
	 * das Recht besitzt.
	 *
	 * @param RightModel $right Das zu prüfende Recht
	 * @param UserModel  $user  Der Benutzer
	 *
	 * @return boolean
	 */
	public static function isAllowed(RightModel $right, UserModel $user)
	{
		self::createRight($right);

		if ($user->getAdmin() || !defined('CHECK_PERMISSIONS') || (APPLICATION_ENV < ENV_PROD && CHECK_PERMISSIONS == false))
		{
			return true;
		}

		$con = Registry::getInstance()->getDatabase();

		$query = $con->newQuery()
			->select('1')
			->from('users as u')
			->innerJoin('right_group_users AS rgu')
			->on('rgu.user_id = u.id')
			->innerJoin('right_group_rights AS rgr')
			->on('rgr.group_id = rgu.group_id')
			->innerJoin('rights AS r')
			->on('r.id = rgr.right_id')
			->addWhere('u.id', $user->getId())
			->addWhere('r.module', $right->getModule())
			->addWhere('r.controller', $right->getController())
			->addWhere('r.action', $right->getAction())
			->addWhere('r.prefix', $right->getPrefix())
			->limit(0, 1);

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);
		return $rsExecution->get();
	}

	/**
	 * Erstellt ein neues Recht. Falls es schon existiert wird die "Modified"-Eigenschaft
	 * aktualisiert
	 *
	 * @param App\Models\Right|array $right Rechte-Daten
	 *
	 * @throws \InvalidArgumentException Wenn die Rechte-Daten in einem umbekannten Format
	 * übergeben werden
	 *
	 * @return boolean
	 */
	public static function createRight($right)
	{
		$sql = "
			INSERT INTO
				rights
				(`module`,`controller`,`action`,`prefix`,`modified`)
			VALUES
				('%s', '%s', '%s', '%s', NOW())
			ON DUPLICATE KEY UPDATE
				`modified` = NOW()
		";

		if ($right instanceof RightModel)
		{
			$queryString = sprintf(
				$sql,
				mysql_real_escape_string($right->getModule()),
				mysql_real_escape_string($right->getController()),
				mysql_real_escape_string($right->getAction()),
				mysql_real_escape_string($right->getPrefix())
			);
		}
		else if (is_array($right) && !empty($right))
		{
			$queryString = sprintf(
				$sql,
				mysql_real_escape_string($right['module']),
				mysql_real_escape_string($right['controller']),
				mysql_real_escape_string($right['action']),
				mysql_real_escape_string($right['prefix'])
			);
		}
		else
		{
			throw new \InvalidArgumentException('Invalid right definition');
		}

		$con = Registry::getInstance()->getDatabase();
		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute(
			$con->newQuery()->setQueryOnce($queryString)
		);

		return $rsExecution->isSuccessful();

	}

	/**
	 * Liefert alle Rechte
	 *
	 * @return App\Models\Right[]
	 */
	public static function getAllRights()
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()
			->select(
				'id,
				title,
				prefix,
				module,
				controller,
				action,
				modified'
			)
			->from('rights')
			->orderBy('prefix,module,controller,action');

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
	 * @return App\Models\Right[]
	 */
	public static function getRightsByMultipleIds(array $ids)
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()
			->select(
				'id,
				title,
				module,
				controller,
				action,
				modified'
			)
			->from('rights')
			->addWhere('id', $ids);

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
		return $con->update(
			'rights',
			array(
				'title' => $right->getTitle(),
				'module' => $right->getModule(),
				'controller' => $right->getController(),
				'action' => $right->getAction(),
				'prefix' => $right->getPrefix(),
				'modified' => $datetime->format('Y-m-d H:i:s'),
				'id' => $right->getId()
			)
		);
	}

	/**
	 * Liefert alle Rechte einer Rolle
	 *
	 * @param integer $groupId ID der zugehörigen Gruppe
	 *
	 * @return App\Models\Right[]
	 */
	public static function getRightsByGroupId($groupId)
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()
			->select(
				'r.id,
				r.title,
				r.module,
				r.controller,
				r.action,
				r.modified'
			)
			->from('rights AS r')
			->innerJoin('right_group_rights AS rgr')
			->on('rgr.right_id = r.id')
			->addWhere('rgr.group_id', $groupId);

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

?>