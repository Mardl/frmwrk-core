<?php
/**
 * User Manager
 *
 * PHP version 5.3
 *
 * @category Manager
 * @package  Core\Application\Manager
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace Core\Application\Manager;

use Core\Application\Models\User as UserModel,
	jamwork\common\Registry,
	jamwork\database\MysqlRecordset as Recordset;

/**
 * User
 *
 * @category Manager
 * @package  Core\Application\Manager
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class User
{

	/**
	 * Users
	 *
	 * @var array
	 */
	private static $_users = array();

	/**
	 * Liefert einen Benutzer anhand seiner Id
	 *
	 * @param integer $userid Benutzerid
	 *
	 * @throws \InvalidArgumentException Wenn keine Id übergeben wurde
	 * @throws \Exception Wenn der Benutzer nicht gefunden wurde
	 *
	 * @return App\Models\User
	 */
	public static function getUserById($userid)
	{
		if (empty($userid))
		{
			throw new \InvalidArgumentException('Invalid Userid');
		}

		if (array_key_exists($userid, self::$_users))
		{
			return self::$_users[$userid];
		}

		$con = Registry::getInstance()->getDatabase();

		$query = $con->newQuery()
			->select(
				'u.id,
				u.username,
				u.firstname,
				u.lastname,
				u.email,
				u.email_corrupted AS emailCorrupted,
				u.avatar,
				u.birthday,
				u.gender,
				u.created,
				u.status,
				u.admin,
				u.otp,
				u.language'
			)
			->from('users as u')
			->addWhere('id', $userid)
			->limit(0, 1);

		$rs = new RecordSet();
		$rsExecution = $rs->execute($query);

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			$rs = $rsExecution->get();
			self::$_users[$userid] = new UserModel($rs);
			return self::$_users[$userid];
		}

		throw new \ErrorException('Benutzer mit ID: '.$userid.' nicht gefunden');
	}

	/**
	 * Liefert ein Array mit Benutzern anhand deren Ids
	 *
	 * @param array $userids Benutzerids
	 *
	 * @throws \InvalidArgumentException Wenn das Array leer übergeben wurde
	 *
	 * @return App\Models\User[]
	 */
	public static function getUserByIds($userids)
	{
		if (empty($userids))
		{
			throw new \InvalidArgumentException('Empty array');
		}

		$con = Registry::getInstance()->getDatabase();

		$query = $con->newQuery()
			->select(
				'u.id,
				u.username,
				u.firstname,
				u.lastname,
				u.email,
				u.email_corrupted AS emailCorrupted,
				u.avatar,
				u.birthday,
				u.gender,
				u.created,
				u.status,
				u.admin,
				u.otp,
				u.language'
			)
			->from('users as u')
			->addWhere('id', $userids);

		$rs = new RecordSet();
		$rsExecution = $rs->execute($query);

		$users = array();
		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			while (($rs = $rsExecution->get()) == true){
				$users[] = new UserModel($rs);
			}
		}

		return $users;
	}

	/**
	 * Liefert einen Benutzer anhand seines Benutzernamens
	 *
	 * @param string $username Benutzername
	 *
	 * @throws \InvalidArgumentException Wenn kein Benutzername übergeben wurde
	 * @throws \Exception Wenn der Benutzer nicht gefunden wurde
	 *
	 * @return App\Models\User
	 */
	public static function getUserByUsername($username)
	{
		if (empty($username))
		{
			throw new \InvalidArgumentException('Invalid Username');
		}

		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()
			->select(
				'u.id,
				u.username,
				u.firstname,
				u.lastname,
				u.email,
				u.email_corrupted AS emailCorrupted,
				u.avatar,
				u.birthday,
				u.gender,
				u.created,
				u.status,
				u.admin,
				u.otp,
				u.language'
			)
			->from('users as u')
			->addWhere('username', $username)
			->limit(0, 1);

		$rs = new RecordSet();
		$rsExecution = $rs->execute($query);

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			$rs = $rsExecution->get();
			return new UserModel($rs);

		}

		throw new \ErrorException('Benutzer nicht gefunden');
	}

	/**
	 * Liefert einen Benutzer anhand seines Benutzernamens
	 *
	 * @param string $username Benutzername
	 *
	 * @throws \InvalidArgumentException Wenn kein Benutzername übergeben wurde
	 * @throws \Exception Wenn der Benutzer nicht gefunden wurde
	 *
	 * @return App\Models\User
	 */
	public static function getUserByEMail($email)
	{
		if (empty($email))
		{
			throw new \InvalidArgumentException('Invalid eMail');
		}

		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()
			->select(
				'u.id,
				u.username,
				u.firstname,
				u.lastname,
				u.email,
				u.email_corrupted AS emailCorrupted,
				u.avatar,
				u.birthday,
				u.gender,
				u.created,
				u.status,
				u.admin,
				u.otp,
				u.language'
		)
			->from('users as u')
			->addWhere('email', $email)
			->limit(0, 1);

		$rs = new RecordSet();
		$rsExecution = $rs->execute($query);

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			$rs = $rsExecution->get();
			return new UserModel($rs);

		}

		throw new \ErrorException('E-Mail Adresse konnte nicht gefunden werden!');
	}

	public static function checkPassword($userid, $password)
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()
			->select('id, password, otp')
			->from('users')
			->addWhere('id', $userid)
			->addWhere('status', STATUS_ACTIVE);

		$rs = new RecordSet();
		$rsExecution = $rs->execute($query);

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			$rs = $rsExecution->get();
			$checkup = false;

			if (strlen($rs['password']) <= 32){
				$checkup = (md5($password) == $rs['password']);
			}
			else
			{
				$checkup = \Core\String::bcryptCheckup($password, $rs['password']);
			}

			return $checkup;

		}

		return false;
	}
	/**
	 * Sucht einen Benutzer mittels Benutzername und Passwort.
	 * Wenn dies klappt wird die Benutzer-Id in der Session gespeichert und
	 * die ID zurückgeliefert.
	 *
	 * @param string $username Benutzername
	 * @param string $password Passwort
	 *
	 * @throws \Exception Wenn mit den angegebenen Daten kein Benutzer gefunden wird
	 *
	 * @return integer
	 */
	public static function login($username, $password)
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()
			->select('id, password, otp')
			->from('users')
			->addWhere('username', $username)
			->addWhere('status', STATUS_ACTIVE);

		$rs = new RecordSet();
		$rsExecution = $rs->execute($query);

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			$rs = $rsExecution->get();
			$checkup = false;

			if (strlen($rs['password']) <= 32){
				$checkup = (md5($password) == $rs['password']);
			}
			else
			{
				$checkup = \Core\String::bcryptCheckup($password, $rs['password']);
			}

			if ($checkup)
			{
				$session = Registry::getInstance()->getSession();
				$session->set('user', $rs['id']);
				$session->set('otp', $rs['otp']);
				return $rs['id'];
			}
		}

		throw new \ErrorException('Benutzer nicht gefunden');
	}

	/**
	 * Loggt den Benutzer aus, indem die Session zerstört wird.
	 *
	 * @return void
	 */
	public static function logout()
	{
		Registry::getInstance()->getSession()->destroy();
	}

	/**
	 * Liefert die Anzahl der Benutzer
	 *
	 * @param integer $status Der maximale Benutzerstatus, Default: Deleted
	 *
	 * @return integer
	 */
	public static function getUserCount($status = STATUS_DELETED)
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()
			->select('COUNT(*) AS count')
			->from('users as u')
			->addWhere('status', $status, '<=');

		$rs = new RecordSet();
		$rsExecution = $rs->execute($query);

		$count = 0;
		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			$rs = $rsExecution->get();
			$count = $rs['count'];
		}

		return $count;
	}

	/**
	 * Liefert ein Array mit den Benutzern.
	 * Im ersten Parameter $status wird übermittelt bis (exklusive) welchem Benutzerstatus
	 * die Benutzer aus der Datenbank gelesen werden sollen
	 *
	 * @param integer $status Der maximale Benutzerstatus, Default: Deleted
	 * @param integer $offset Optionaler Offset
	 * @param integer $limit  Optionales Limit
	 *
	 * @return App\Models\User[]
	 */
	public static function getUsers($status = STATUS_DELETED, $offset = 0, $limit = 25)
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()
			->select(
				'u.id,
				u.username,
				u.firstname,
				u.lastname,
				u.email,
				u.email_corrupted AS emailCorrupted,
				u.avatar,
				u.birthday,
				u.gender,
				u.created,
				u.status,
				u.admin,
				u.otp,
				u.language'
			)
			->from('users as u')
			->addWhere('status', $status, '<=')
			->orderBy('username')
			->limit($offset, $limit);

		$query->distinct();

		$rs = new RecordSet();
		$rsExecution = $rs->execute($query);

		$models = array();

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			while (($rec = $rs->get()) == true)
			{
				$models[] = new UserModel($rec);
			}
		}

		return $models;
	}

	/**
	 * Liefert Benutzer einer bestimmten Rolle
	 *
	 * @param integer $groupId ID der Rolle
	 *
	 * @return App\Models\User[]
	 */
	public static function getUsersByGroupId($groupId)
	{
		$con = Registry::getInstance()->getDatabase();

		$query = $con->newQuery()
			->select(
				'u.id,
				u.username,
				u.firstname,
				u.lastname,
				u.email,
				u.email_corrupted AS emailCorrupted,
				u.avatar,
				u.birthday,
				u.gender,
				u.created,
				u.status,
				u.admin,
				u.otp,
				u.language'
			)
			->from('users as u')
			->innerJoin('right_group_users AS rgu')
			->on('rgu.user_id = u.id')
			->addWhere('rgu.group_id', $groupId);


		$rs = new RecordSet();
		$rsExecution = $rs->execute($query);

		$users = array();

		if ($rsExecution->isSuccessfull())
		{
			while (($rs = $rsExecution->get()) == true)
			{
				$users[] = new UserModel($rs);
			}
		}

		return $users;
	}

	/**
	 * Speichert einen neuen Benutzer in der Datenbank
	 *
	 * @param App\Models\User $user     Userobject
	 * @param string          $password Passwort
	 *
	 * @return App\Models\User|boolean
	 */
	public static function insertUser(UserModel $user, $password)
	{
		$con = Registry::getInstance()->getDatabase();
		$datetime = new \DateTime();

		if(!self::checkUniqueUsername($user))
		{
			throw new \ErrorException("Der gewünschte Username ist bereits vergeben!");
		}

		$user->setCreated($datetime);

		$id = $con->insert(
			'users',
			array(
				'username' 	=> $user->getUsername(),
				'firstname' => $user->getFirstname(),
				'lastname' 	=> $user->getLastname(),
				'password' 	=> $user->setPassword($password, false),
				'email' 	=> $user->getEmail(),
				'birthday' 	=> $user->getBirthday()->format('Y-m-d'),
				'gender' 	=> $user->getGender(),
				'created' 	=> $datetime->format('Y-m-d H:i:s'),
				'status' 	=> STATUS_ACTIVE,
				'admin' 	=> $user->getAdmin(),
				'language' 	=> ($user->getLanguage())?$user->getLanguage()->getId():''
			)
		);

		if ($id)
		{
			$user->setId($id);
			return $user;
		}

		return false;
	}

	/**
	 * Speichert Änderungen eines Benutzers
	 *
	 * @param App\Models\User $user     Userobject
	 * @param string          $password Optionales Passwort
	 *
	 * @return App\Models\User|boolean
	 */
	public static function updateUser(UserModel $user, $password = '')
	{
		unset(self::$_users[$user->getId()]);
		$con = Registry::getInstance()->getDatabase();

		if (!$user->getId())
		{
			return false;
		}

		if(!self::checkUniqueUsername($user))
		{
			throw new \ErrorException("Der gewünschte Username ist bereits vergeben!");
		}

		if (empty($password))
		{
			$data = array(
				'username' 	=> $user->getUsername(),
				'firstname' => $user->getFirstname(),
				'lastname' 	=> $user->getLastname(),
				'email' 	=> $user->getEmail(),
				'birthday' 	=> $user->getBirthday()->format('Y-m-d'),
				'gender' 	=> $user->getGender(),
				'avatar' 	=> $user->getAvatarId(),
				'status' 	=> $user->getStatus(),
				'admin' 	=> $user->getAdmin(),
				'otp'	 	=> $user->getOtp(),
				'id' 		=> $user->getId(),
				'language'	=> ($user->getLanguage())?$user->getLanguage()->getId():''
			);
		} else {
			$data = array(
				'username' 	=> $user->getUsername(),
				'firstname' => $user->getFirstname(),
				'lastname' 	=> $user->getLastname(),
				'email' 	=> $user->getEmail(),
				'birthday' 	=> $user->getBirthday()->format('Y-m-d'),
				'gender' 	=> $user->getGender(),
				'avatar' 	=> $user->getAvatarId(),
				'status' 	=> $user->getStatus(),
				'password' 	=> $user->setPassword($password, false),
				'admin' 	=> $user->getAdmin(),
				'otp'	 	=> $user->getOtp(),
				'id' 		=> $user->getId(),
				'language'	=> ($user->getLanguage())?$user->getLanguage()->getId():''
			);
		}

		if ($con->update('users', $data))
		{
			return $user;
		}
		else
		{
			return false;
		}
	}


	/**
	 * Liefert ein Array mit den Benutzern.
	 * Im ersten Parameter $status wird übermittelt bis (exklusive) welchem Benutzerstatus
	 * die Benutzer aus der Datenbank gelesen werden sollen
	 *
	 * @param string  $keyword Suchwort
	 * @param integer $status  Der maximale Benutzerstatus, Default: Deleted
	 *
	 * @return App\Models\User[]
	 */
	public static function searchUsers($keyword, $status = STATUS_DELETED)
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()
			->select(
				'u.id,
				u.username,
				u.firstname,
				u.lastname,
				u.email,
				u.email_corrupted AS emailCorrupted,
				u.avatar,
				u.birthday,
				u.gender,
				u.created,
				u.status,
				u.admin,
				u.language'
			)
			->from('users as u')
			->addWhere('status', $status, '<=')
			->openClosure()
			->addWhereLike('username', $keyword)
			->addWhereLike('firstname', $keyword, '%%%s%%', 'OR')
			->addWhereLike('lastname', $keyword, '%%%s%%', 'OR')
			->closeClosure()
			->orderBy('username');

		$query->distinct();

		$rs = new RecordSet();
		$rsExecution = $rs->execute($query);

		$models = array();

		if ($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			while (($rec = $rs->get()) == true)
			{
				$models[] = new UserModel($rec);
			}
		}

		return $models;
	}

	public static function checkUniqueUsername(UserModel $model)
	{
		$con = Registry::getInstance()->getDatabase();
		$query = $con->newQuery()
			->select('*')
			->from('users')
			->addWhere('username', $model->getUsername());


		$rs = new RecordSet();
		$rsExecution = $rs->execute($query);

		if($rsExecution->isSuccessfull() && ($rsExecution->count() > 0))
		{
			$rsExecution = $rs->get();
			if($rsExecution['id'] != $model->getId())
			{
				return false;
			}
		}

		return true;
	}

	public static function generateOTP($userid)
	{
		$userModel = self::getUserById($userid);

		$crypttime=md5(crypt(time()));
		$randompass=substr($crypttime, 0, 8);
		$randompass = strtolower($randompass);

		$userModel->setOtp(1);
		self::updateUser($userModel,$randompass);

		return $randompass;
	}
}

?>