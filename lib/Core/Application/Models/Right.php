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
 * @Entity
 * @Table(
 * 	name="rights",
 * 	uniqueConstraints={@UniqueConstraint(columns={"module", "controller", "action", "prefix"})}
 * )
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
     * @var datetime
     *
     * @Column(type="datetime")
     */
    protected $modified;

    /**
     * Rights
     *
     * @ManyToMany(targetEntity="Core\Application\Models\Right\Group", mappedBy="rights", cascade={"persist"})
     * @JoinTable(name="right_group_rights")
     */
    protected $groups;

    /**
     * Prüft die Elemente des Arrays auf Typ App\Models\Right\Group
     *
     * @param array $groups Array mit den Rechtegruppen
     *
     * @return void
     */
    public function setGroups(array $groups)
    {
    	if (!empty($groups))
    	{
    		foreach ($groups as $group)
    		{
    			if (!($group instanceof RightGroup))
    			{
    				throw new \InvalidArgumentException("Ungültiger Typ der Rechtegruppe");
    			}
    		}
    	}

    	$this->groups = $groups;
    }

}

?>