<?php

namespace Core\Application\Manager\Right;

use App\Models\Right as RightModel,
	App\Models\Right\Group as GroupModel,
	App\Models\User as UserModel,
	App\Manager\Right as RightManager,
	App\Manager\User as UserManager,
	Core\SystemMessages,
	jamwork\common\Registry;

/**
 * Group
 *
 * @category Core
 * @package  Core\Application\Manager
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class Group
{

	/**
	 * Liefert alle Rechtegruppen
	 *
	 * @return \Core\Application\Models\Right\Group[]
	 */
	public static function getAllGroups()
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()->select(
			'id,
				name,
				modified')->from('right_groups')->orderBy('name');

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		$groups = array();

		if ($rsExecution->isSuccessfull())
		{
			while (($rs = $rsExecution->get()) == true)
			{
				$groups[] = new GroupModel($rs);
			}
		}

		return $groups;
	}

	/**
	 * @param integer $id     Gruppen-ID
	 * @param bool    $rights Optional, liefere auch die Rechte
	 * @param bool    $users  Optional, liefere auch die Mitglieder
	 *
	 * @return \Core\Application\Models\Right\Group
	 *
	 * @throws \ErrorException Wenn die Gruppe nicht gefunden wird
	 */
	public static function getGroupById($id, $rights = false, $users = false)
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()->select('id,
				name,
				modified')->from('right_groups')->addWhere('id', $id)->limit(0, 1);

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		if ($rsExecution->isSuccessfull() && $rsExecution->count() == 1)
		{
			$group = new GroupModel($rsExecution->get());
		}
		else
		{
			throw new \ErrorException("Rolle mit der ID $id nicht gefunden°");
		}

		if ($rights)
		{
			$group->setRights(RightManager::getRightsByGroupId($group->getId()));
		}

		if ($users)
		{
			$group->setUsers(UserManager::getUsersByGroupId($group->getId()));
		}

		return $group;
	}

	/**
	 * Liefert ein Array mit Gruppen anhand übermittelter IDs
	 *
	 * @param array   $ids    Gruppen-IDs
	 * @param bool $rights Optional, liefere auch die Rechte
	 * @param bool $users  Optional, liefere auch die Mitglieder
	 *
	 * @throws \ErrorException Wenn beim Abruf ein Fehler auftritt
	 *
	 * @return \App\Models\Right\Group[]
	 */
	public static function getGroupsByIds(array $ids, $rights = false, $users = false)
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()->select('id,
				name,
				modified')->from('right_groups')->addWhere('id', $ids);

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		$groups = array();

		if ($rsExecution->isSuccessfull() && $rsExecution->count() >= 1)
		{
			while (($data = $rsExecution->get()) == true)
			{
				$group = new GroupModel($data);

				if ($rights)
				{
					$group->setRights(RightManager::getRightsByGroupId($group->getId()));
				}

				if ($users)
				{
					$group->setUsers(UserManager::getUsersByGroupId($group->getId()));
				}

				$groups[] = $group;
			}
		}
		else
		{
			throw new \ErrorException("Beim Abruf der Gruppen (IDs '" . implode(',', $ids) . "') ist ein Fehler aufgetreten");
		}

		return $groups;
	}

	/**
	 * Liefert die Gruppe anhand ihres Namens
	 *
	 * @static
	 *
	 * @param string $name
	 * @param bool   $rights
	 * @param bool   $users
	 *
	 * @return \Core\Application\Models\Right\Group
	 *
	 * @throws \ErrorException Wenn die Gruppe nicht gefunden wird
	 */
	public static function getGroupByName($name, $rights = false, $users = false)
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()->select('id,
				name,
				modified')->from('right_groups')->addWhere('name', $name)->limit(0, 1);

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		if ($rsExecution->isSuccessfull() && $rsExecution->count() == 1)
		{
			$group = new GroupModel($rsExecution->get());
		}
		else
		{
			throw new \ErrorException("Rolle mit dem Namen: $name nicht gefunden!");
		}

		if ($rights)
		{
			$group->setRights(RightManager::getRightsByGroupId($group->getId()));
		}

		if ($users)
		{
			$group->setUsers(UserManager::getUsersByGroupId($group->getId()));
		}

		return $group;
	}

	/**
	 * Liefert die Gruppe anhand ihres Namens
	 *
	 * @param UserModel $user   Benutzerobjekt
	 * @param bool      $rights Optional, liefert auch die Rechte
	 * @param bool      $users  Optional, liefert auch die Mitglieder
	 *
	 * @return array
	 */
	public static function getGroupsByUser(UserModel $user, $rights = false, $users = false)
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()->select('rg.id,
				rg.name,
				rg.modified')->from('right_groups AS rg')->innerJoin('right_group_users AS rgu')->on('rgu.group_id = rg.id')->addWhere('rgu.user_id', $user->getId());

		$rs = $con->newRecordSet();
		$rsExecution = $rs->execute($query);

		$groups = array();

		if ($rsExecution->isSuccessfull())
		{
			while (($data = $rsExecution->get()) == true)
			{
				$group = new GroupModel($data);

				if ($rights)
				{
					$group->setRights(RightManager::getRightsByGroupId($group->getId()));
				}

				if ($users)
				{
					$group->setUsers(UserManager::getUsersByGroupId($group->getId()));
				}

				$groups[] = $group;
			}
		}

		return $groups;
	}

	/**
	 * Erstellt eine Gruppe
	 *
	 * @param \Core\Application\Models\Right\Group $group Zu erstellende Gruppe
	 *
	 * @return bool|\Core\Application\Models\Right\Group
	 */
	public static function createGroup(\Core\Application\Models\Right\Group $group)
	{

		try
		{
			$g = self::getGroupByName($group->getName());
			if ($g)
			{
				SystemMessages::addError('Eine Rolle mit dem Namen "' . $group->getName() . '" existiert bereits!');

				return false;
			}
		} catch (\ErrorException $e)
		{
			/*** Ist ok ***/
		}

		$con = Registry::getInstance()->getDatabase();
		$datetime = new \DateTime();

		$inserted = $con->insert('right_groups',
			array(
				'name' => $group->getName(),
				'modified' => $datetime->format('Y-m-d H:i:s')
             )
		);

		if (!$inserted)
		{
			SystemMessages::addError('Beim Erstellen der Rolle ist ein Fehler aufgetreten!');

			return false;
		}

		$usergroup = self::getGroupById($inserted);

		return $usergroup;
	}

	/**
	 * Aktualisiert eine Gruppe
	 *
	 * @param GroupModel $group       Zu aktualisierende Gruppe
	 * @param bool       $forceRights Rechte erzwingen
	 * @param bool       $forceUser   Benutzer erzwingen
	 *
	 * @return bool
	 */
	public static function updateGroup(GroupModel $group, $forceRights = false, $forceUser = false)
	{
		$con = Registry::getInstance()->getDatabase();
		$datetime = new \DateTime();

		$updated = $con->update('right_groups', array(
				'name' => $group->getName(),
				'modified' => $datetime->format('Y-m-d H:i:s'),
				'id' => $group->getId()
			)
		);

		if (!$updated)
		{
			SystemMessages::addError('Beim Update der Rolle ist ein Fehler aufgetreten!');

			return false;
		}

		$rights = $group->getRights();

		if (!empty($rights) || $forceRights)
		{
			if (!self::updateGroupRights($group))
			{
				SystemMessages::addError('Beim Update der Rechte ist ein Fehler aufgetreten!');

				return false;
			}
		}

		$users = $group->getUsers();

		if (!empty($users) || $forceUser)
		{
			if (!self::updateGroupUsers($group))
			{
				SystemMessages::addError('Beim Update der Mitglieder ist ein Fehler aufgetreten!');

				return false;
			}
		}

		return true;
	}

	/**
	 * Aktualisiert die Rechte einer Gruppe
	 *
	 * @param GroupModel $group zu aktualisierende Gruppe
	 * @return bool
	 */
	public static function updateGroupRights(GroupModel $group)
	{
		$rights = $group->getRights();

		$delete = "
			DELETE FROM
				right_group_rights
			WHERE
				group_id = %d;
		";

		$insert = "
			INSERT INTO
				right_group_rights
				(`group_id`, `right_id`)
			VALUES
				%s;
		";

		$values = array();
		$value = "(%d, %d)";

		/**
		 * mysql_real_escape_string wird hier nicht benötigt, Daten kommen bereits aus der Datenbank
		 *
		foreach ($rights as $right)
		{
			$values[] = sprintf($value, mysql_real_escape_string($group->getId()), mysql_real_escape_string($right->getId()));
		}

		$deleteQuery = sprintf($delete, mysql_real_escape_string($group->getId()));
		*/
		foreach ($rights as $right)
		{
			$values[] = sprintf($value, $group->getId(), $right->getId());
		}

		$deleteQuery = sprintf($delete, $group->getId());
		$insertQuery = '';

		if (!empty($values))
		{
			$insertQuery = sprintf($insert, implode(',', $values));
		}

		$con = Registry::getInstance()->getDatabase();
		$rs = $con->newRecordSet();

		// Starte Transaktion
		$con->startTransaction();

		// Lösche die bestehenden Rechte
		$execDelete = $rs->execute($con->newQuery()->setQueryOnce($deleteQuery));

		// Verbinde Rechte und Gruppe neu
		if ($execDelete->isSuccessfull())
		{
			if (!empty($values))
			{
				$execInsert = $rs->execute($con->newQuery()->setQueryOnce($insertQuery));
			}
			if (empty($values) || $execInsert->isSuccessfull())
			{
				// Schließe Transaktion erfolgreich ab
				$con->commit();

				return true;
			}
			else
			{
				// Führe Rollback durch
				$con->rollback();

				return false;
			}
		}
		else
		{
			// Führe Rollback durch
			$con->rollback();

			return false;
		}
	}

	/**
	 * @param UserModel $user
	 * @param array     $groups
	 * @return bool
	 */
	public static function updateUsersGroups(UserModel $user, array $groups)
	{
		$delete = "
			DELETE FROM
				right_group_users
			WHERE
				user_id = %d;
		";
		$insert = "
			INSERT INTO
				right_group_users
				(`group_id`, `user_id`)
			VALUES
				%s;
		";

		$values = array();
		$value = "(%d, %d)";

		/**
		 * mysql_real_escape_string wird hier nicht benötigt, Daten kommen bereits aus der Datenbank
		 *
		foreach ($groups as $group)
		{
			$values[] = sprintf($value, mysql_real_escape_string($group->getId()), mysql_real_escape_string($user->getId()));
		}

		$deleteQuery = sprintf($delete, mysql_real_escape_string($user->getId()));
		 */

		foreach ($groups as $group)
		{
			$values[] = sprintf($value, $group->getId(), $user->getId());
		}

		$deleteQuery = sprintf($delete, $user->getId());

		$insertQuery = '';
		if (!empty($values))
		{
			$insertQuery = sprintf($insert, implode(',', $values));
		}

		$con = Registry::getInstance()->getDatabase();
		$rs = $con->newRecordSet();

		// Starte Transaktion
		$con->startTransaction();

		// Lösche die bestehenden Mitglieder
		$execDelete = $rs->execute($con->newQuery()->setQueryOnce($deleteQuery));

		// Verbinde Mitglieder und Gruppe neu
		if ($execDelete->isSuccessfull())
		{
			if (!empty($values))
			{
				$execInsert = $rs->execute($con->newQuery()->setQueryOnce($insertQuery));
			}
			if (empty($values) || $execInsert->isSuccessfull())
			{
				// Schließe Transaktion erfolgreich ab
				$con->commit();

				return true;
			}
			else
			{
				//Führe Rollback durch
				$con->rollback();

				return false;
			}
		}
		else
		{
			// Führe Rollback durch
			$con->rollback();

			return false;
		}
	}

	/**
	 * Aktualisiert die Mitglieder einer Gruppe
	 *
	 * @param GroupModel $group zu aktualisierende Gruppe
	 *
	 * @return bool
	 */
	public static function updateGroupUsers(GroupModel $group)
	{
		$users = $group->getUsers();

		$delete = "
			DELETE FROM
				right_group_users
			WHERE
				group_id = %d;
		";

		$insert = "
			INSERT INTO
				right_group_users
				(`group_id`, `user_id`)
			VALUES
				%s;
		";

		$values = array();
		$value = "(%d, %d)";
		/**
		 * mysql_real_escape_string wird hier nicht benötigt, Daten kommen bereits aus der Datenbank
		 *
		foreach ($users as $user)
		{
			$values[] = sprintf($value, mysql_real_escape_string($group->getId()), mysql_real_escape_string($user->getId()));
		}

		$deleteQuery = sprintf($delete, mysql_real_escape_string($group->getId()));
		 */
		foreach ($users as $user)
		{
			$values[] = sprintf($value, $group->getId(), $user->getId());
		}

		$deleteQuery = sprintf($delete, $group->getId());
		$insertQuery = '';
		if (!empty($values))
		{
			$insertQuery = sprintf($insert, implode(',', $values));
		}

		$con = Registry::getInstance()->getDatabase();
		$rs = $con->newRecordSet();

		// Starte Transaktion
		$con->startTransaction();

		// Lösche die bestehenden Mitglieder
		$execDelete = $rs->execute($con->newQuery()->setQueryOnce($deleteQuery));

		// Verbinde Mitglieder und Gruppe neu
		if ($execDelete->isSuccessfull())
		{
			if (!empty($values))
			{
				$execInsert = $rs->execute($con->newQuery()->setQueryOnce($insertQuery));
			}
			if (empty($values) || $execInsert->isSuccessfull())
			{
				// Schließe Transaktion erfolgreich ab
				$con->commit();

				return true;
			}
			else
			{
				// Führe Rollback durch
				$con->rollback();

				return false;
			}
		}
		else
		{
			// Führe Rollback durch
			$con->rollback();

			return false;
		}
	}

	/**
	 * Fügt einen einzelnen Benutzer zur Gruppe hinzu
	 *
	 * @param GroupModel $group Betreffende Gruppe
	 * @param UserModel  $user  Betreffender Benutzer
	 * @return mixed
	 */
	public static function addUserToGroup(GroupModel $group, UserModel $user)
	{
		$con = Registry::getInstance()->getDatabase();

		return $con->insert('right_group_users',
			array(
				'group_id' => $group->getId(),
				'user_id' => $user->getId()
			)
		);
	}
}
