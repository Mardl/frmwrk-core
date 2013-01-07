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
namespace Core\Application\Models;

use Core\Model as BaseModel,
	Core\Application\Models\Right\Group as RightGroup;

/**
 * Right
 *
 * @category Model
 * @package  Models
 * @author   Alexander Jonser <alex@dreiwerken.de>
 *
 * @method string getTitle()
 * @method string getModule()
 * @method string getController()
 * @method string getAction()
 * @method string getPrefix()
 * @method string getModified()
 * @method \Core\Application\Models\Right\Group getGroups()
 *
 * @method setTitle($value)
 * @method setModule($value)
 * @method setController($value)
 * @method setAction($value)
 * @method setPrefix($value)
 *
 * @MappedSuperclass
 */
class Right extends BaseModel
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
     * @Column(type="string")
     */
    protected $title;

    /**
     * Module
     *
     * @var string
     *
     * @Column(type="string")
     */
    protected $module;

    /**
     * Controller
     *
     * @var string
     *
     * @Column(type="string")
     */
    protected $controller;

    /**
     * Action
     *
     * @var string
     *
     * @Column(type="string")
     */
    protected $action;

    /**
     * Prefix
     *
     * @var string
     *
     * @Column(type="string")
     */
    protected $prefix = '';

    /**
     * Modified
     *
     * @var \DateTime
     *
     * @Column(type="datetime")
     */
    protected $modified;

    /**
     * Rights
     *
     * @ManyToMany(targetEntity="App\Models\Right\Group")
     * @JoinTable(name="right_group_rights")
     */
    protected $groups;

	/**
	 * Prüft die Elemente des Arrays auf Typ App\Models\Right\Group
	 *
	 * @param array $groups Array mit den Rechtegruppen
	 *
	 * @throws \InvalidArgumentException
	 */
	public function setGroups(array $groups)
    {
    	if (!empty($groups))
    	{
    		foreach ($groups as $group)
    		{
    			if (!($group instanceof RightGroup))
    			{
    				throw new \InvalidArgumentException(translate('Ungültiger Typ der Rechtegruppe'));
    			}
    		}
    	}

    	$this->groups = $groups;
    }
}
