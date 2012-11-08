<?php
/**
 * Model Directories
 *
 * PHP version 5.3
 *
 * @category Model
 * @package  Models
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace Core\Application\Models;

use Core\Model as BaseModel,
	Core\Application\Manager\Directory as DirectoryManager,
	Core\Application\Models\Directory as DirectoryModel;


/**
 * Directories
 *
 * @category Model
 * @package  Models
 * @author   Alexander Jonser <alex@dreiwerken.de>
 *
 * @method string getName()
 * @method DirectoryModel getParent()
 * @method int getSort()
 *
 * @method setName($value)
 * @method setParent(DirectoryModel $value)
 * @method setSort($value)
 *
 * @MappedSuperclass
 */
class Directory extends BaseModel
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
	 * Directory Name
	 *
	 * @var string
	 *
	 * @Column(type="string", length=255)
	 */
	protected $name;

	/**
	 * Parent
	 *
	 * @var App\Models\Directory
	 *
	 * @ManyToOne(targetEntity="App\Models\Directory")
	 */
	protected $parent;

	/**
	 * Street
	 *
	 * @var integer
	 *
	 * @Column(type="integer", length=3)
	 */
	protected $sort;

	/**
	 * Setzt das Parent Directory
	 *
	 * @param Core\Application\Models\Directory $parentDirectory Directory Model des Parents
	 *
	 * @return void
	 */
	public function setParentDirectory(DirectoryModel $parentDirectory)
	{
		$this->parent = $parentDirectory;
	}

	/**
	 * @param Directory $parentDirectory
	 * @deprecated
	 */
	public function setParent(DirectoryModel $parentDirectory)
	{
		$this->setParentDirectory($parentDirectory);
	}

	/**
	 * Liefert das Parent Directory
	 *
	 * @return Core\Application\Models\Directory
	 */
	public function getParentDirectory()
	{
		return $this->parent;
	}

	/**
	 * @return App\Models\Directory
	 * @deprecated
	 */
	public function getParent()
	{
		return $this->getParentDirectory();
	}

	/**
	 * Handelt es sich um einen Root Type
	 *
	 * @return bool
	 */
	public function isRootType()
	{
		return empty($this->parent);
	}

	/**
	 * Liefert ein array mit allen Children des Directory (Directories und Files)
	 *
	 * @return array()
	 */
	public function getChildren()
	{
		return DirectoryManager::getChildren($this->id);
	}

	/**
	 * Liefert die IDs des Elternbaums
	 *
	 * @return string
	 */
	public function getParentIds()
	{
		if (!is_null($this->parent))
		{
			return ($this->parent->getParentIds().','.$this->parent->getId());
		}
		return 0;
	}

	/**
	 * Liefert den Typen zurück
	 *
	 * @return string
	 */
	public function getTyp()
	{
		return 'folder';
	}
}
