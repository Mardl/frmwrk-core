<?php

namespace Core\Application\Models\Right;

use Core\Model as BaseModel, App\Models\Right as RightModel, App\Models\User as UserModel;

/**
 * Class Group
 *
 * @category Core
 * @package  Core\Application\Models\Right
 * @author   Alexander Jonser <alex@dreiwerken.de>
 *
 * @method string getName()
 * @method string getModified()
 * @method array getRights()
 * @method array getUsers()
 *
 * @method setName($value)
 * @method setModified($value)
 *
 * @MappedSuperclass
 */
class Group extends BaseModel
{

	/**
	 * Id
	 *
	 * @var integer
	 *
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * Module
	 *
	 * @var string
	 *
	 * @Column(type="string", unique=true)
	 */
	protected $name;

	/**
	 * Modified
	 *
	 * @var \DateTime
	 *
	 * @Column(type="datetime")
	 */
	protected $modified;

	/**
	 * @var array
	 */
	protected $rights;

	/**
	 * Users
	 *
	 * @ManyToMany(targetEntity="App\Models\User")
	 * @JoinTable(name="right_group_users")
	 */
	protected $users;

	/**
	 * Prüft die Elemente des Arrays auf Typ App\Models\Right
	 *
	 * @param array $rights Zu setzende Rechte
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	public function setRights(array $rights)
	{
		if (!empty($rights))
		{
			foreach ($rights as $right)
			{
				if (!($right instanceof RightModel))
				{
					throw new \InvalidArgumentException("Ungültiger Typ vom Recht!");
				}
			}
		}

		$this->rights = $rights;
	}

	/**
	 * Prüft die ELemente des Arrays auf Typ App\Models\User
	 *
	 * @param array $users Zu setzende Benutzer
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	public function setUsers(array $users)
	{
		if (!empty($users))
		{
			foreach ($users as $user)
			{
				if (!($user instanceof UserModel))
				{
					throw new \InvalidArgumentException("Kein User!");
				}
			}
		}

		$this->users = $users;
	}
}
