<?php
/**
 * Model Address
 *
 * PHP version 5.3
 *
 * @category Model
 * @package  Models
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace Core\Application\Models\Right;

use Core\Model as BaseModel,
	Core\Application\Models\Right as RightModel,
	Core\Application\Models\User as UserModel;

/**
 * Right
 *
 * @category Model
 * @package  Models
 * @author   Alexander Jonser <alex@dreiwerken.de>
 *
 * @Entity
 * @Table(name="right_groups")
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
     * @var datetime
     *
     * @Column(type="datetime")
     */
    protected $modified;

    /**
     * Rights
     *
     * @ManyToMany(targetEntity="Core\Application\Models\Right", inversedBy="groups", cascade={"persist"})
     * @JoinTable(name="right_group_rights")
     */
	protected $rights;

	/**
	 * Users
	 *
	 * @ManyToMany(targetEntity="Core\Application\Models\User", inversedBy="rightGroups", cascade={"persist"})
	 * @JoinTable(name="right_group_users")
	 */
	protected $users;

	/**
     * Prüft die Elemente des Arrays auf Typ App\Models\Right
     *
     * @param array $rights Zu setzende Rechte
     *
     * @return void
     */
    public function setRights(array $rights)
    {
    	if (!empty($rights))
    	{
    		foreach ($rights as $right)
    		{
    			if (!($right instanceof RightModel))
    			{
    				throw new \InvalidArgumentException("Ungültiger Typ vom Recht");
    			}
    		}
    	}

    	$this->rights = $rights;
    }

    /**
     * Prüft die Elemente des Arrays auf Typ App\Models\User
     *
     * @param array $users Zu setzende Benutzer
     *
     * @return void
     */
    public function setUsers(array $users)
    {
    	if (!empty($users))
    	{
    		foreach ($users as $user)
    		{
    			if (!($user instanceof UserModel))
    			{
    				throw new \InvalidArgumentException("Kein User");
    			}
    		}
    	}

    	$this->users = $users;
    }
}

?>