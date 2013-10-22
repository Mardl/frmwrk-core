<?php

namespace Core\Application\Models;

use Core\Model as BaseModel, Core\Application\Models\Right\Group as RightGroup;

/**
 * Right
 *
 * @category Core
 * @package  Core\Application\Models
 * @author   Alexander Jonser <alex@dreiwerken.de>
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
	protected $moduletitle;

	/**
	 * Controller
	 *
	 * @var string
	 *
	 * @Column(type="string")
	 */
	protected $controllertitle;

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
	 * Inaktiv
	 *
	 * @var smallint
	 *
	 * @Column(type="smallint")
	 */
	protected $inactive = 0;

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
	 * @return void
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
					throw new \InvalidArgumentException('Ungültiger Typ der Rechtegruppe!');
				}
			}
		}

		$this->groups = $groups;
	}

	/**
	 * @return \Core\Application\Models\Right\Group
	 */
	public function getGroups()
	{
		return $this->groups;
	}

	/**
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @param string $moduletitle
	 * @return void
	 */
	public function setModuletitle($moduletitle)
	{
		$this->moduletitle = $moduletitle;
	}

	/**
	 * @param string $controllertitle
	 * @return void
	 */
	public function setControllertitle($controllertitle)
	{
		$this->controllertitle = $controllertitle;
	}

	/**
	 * @param string $module
	 * @return void
	 */
	public function setModule($module)
	{
		$this->module = $module;
	}

	/**
	 * @param string $controller
	 * @return void
	 */
	public function setController($controller)
	{
		$this->controller = $controller;
	}

	/**
	 * @param string $action
	 * @return void
	 */
	public function setAction($action)
	{
		$this->action = $action;
	}

	/**
	 * @param string $prefix
	 * @return void
	 */
	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;
	}

	/**
	 * @param int $inactive
	 * @return void
	 */
	public function setInactive($inactive)
	{
		$this->inactive = $inactive;
	}


	/**
	 * @return the $title
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @return the $moduletitle
	 */
	public function getModuletitle()
	{
		return $this->moduletitle;
	}

	/**
	 * @return the $controllertitle
	 */
	public function getControllertitle()
	{
		return $this->controllertitle;
	}

	/**
	 * @return the $module
	 */
	public function getModule()
	{
		return $this->module;
	}

	/**
	 * @return the $controller
	 */
	public function getController()
	{
		return $this->controller;
	}

	/**
	 * @return the $action
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @return the $prefix
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}

	/**
	 * @return the $inactive
	 */
	public function getInactive()
	{
		return $this->inactive;
	}


}
