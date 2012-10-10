<?php
/**
 * Model Files
 *
 * PHP version 5.3
 *
 * @category Model
 * @package  Models
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */

namespace Core\Application\Models\Directory;

use Core\Model as BaseModel,
	Core\Application\Manager\Directory\Files as FileManager;

/**
 * Files
 *
 * @category Model
 * @package  Models
 * @author   Alexander Jonser <alex@dreiwerken.de>
 *
 * @MappedSuperclass
 */
class Files extends BaseModel
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
	 * Original Filename
	 *
	 * @var string
	 *
	 * @Column(type="string", length=255)
	 */
	protected $orgname;

	/**
	 * Generated Name
	 *
	 * @var string
	 *
	 * @Column(type="string", length=255)
	 */
	protected $name;

	/**
	 * Generated Name
	 *
	 * @var string
	 *
	 * @Column(type="string", length=255)
	 */
	protected $basename;

	/**
	 * Parent
	 *
	 * @var App\Models\Directory
	 *
	 * @ManyToOne(targetEntity="Core\Application\Models\Directory")
	 * @JoinColumn(name="directory_id", referencedColumnName="id", nullable=false)
	 */
	protected $directory;

	/**
	 * Parent
	 *
	 * @var App\Models\Directory\Files
	 *
	 * @ManyToOne(targetEntity="Core\Application\Models\Directory\Files")
	 * @JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
	 */
	protected $parent;

	/**
	 * Generated Name
	 *
	 * @var string
	 *
	 * @Column(type="string", length=255)
	 */
	protected $mimetype;

	/**
	 * Generated Name
	 *
	 * @var string
	 *
	 * @Column(type="float")
	 */
	protected $size;


	/**
	 * Liefert den Typen zurück
	 *
	 * @return string
	 */
	public function getTyp()
	{
		return 'file';
	}

	/**
	 * Handelt es sich um einen Root Type
	 *
	 * @return bool
	 */
	public function isRootType()
	{
		return false;
	}

	/**
	 * Liefert die IDs des Elternbaums
	 *
	 * @return string
	 */
	public function getParentIds()
	{
		if (!is_null($this->directory))
		{
			return ($this->directory->getParentIds().','.$this->directory->getId());
		}
		return 0;
	}

	/**
	 * Liefert ein leeres Array. (Files haben keine Children)
	 *
	 * @return array()
	 */
	public function getChildren()
	{
		return array();
	}

	public function getThumbnail($width = 128, $height = 128, $alt = '', $style = 'margin: 0px 10px 10px 0px;', $additional = null)
	{
		if (is_null($this->name)){
			return;
		}

		if (!preg_match('/.*video\/.*/', $this->getMimetype()))
		{
			$fm = new FileManager();
			$thumb = $fm->getThumbnail($this, $width, $height);

			return "<img src='".$thumb."' alt='".$alt."' style='".$style."' ".$additional." />";
		}
		else
		{
			$sources = $this->getSources();
			$visu = '';
			if (!empty($sources)){
				$visu = '<video height="'.$height.'" autoplay="autoplay" {poster}loop="loop" style="'.$style.'">';
				foreach ($sources as $source)
				{
					if (!preg_match('/.*video\/.*/', $source[1])){
						$visu = str_replace("{poster}", 'poster="/files/'.$source[0].'" ', $visu);
					} else {
						$visu .= '<source src="/files/'.$source[0].'" type="'.$source[1].'" style="'.$style.'" />';
					}
				}
				$visu = str_replace("{poster}", '', $visu);
				$visu .= 'Your browser does not support the video tag.';
				$visu .= '</video>';



			}
			return $visu;
		}
		//throw new \ErrorException('Bei Videos gibts kein Thumbnail');

	}

	public function getThumbnailTarget($width = 128, $height = 128)
	{
		if (!preg_match('/.*video\/.*/', $this->getMimetype()))
		{
			$fm = new FileManager();
			$thumb = $fm->getThumbnail($this, $width, $height);

			return $thumb;
		}
		else
		{
			return false;
		}

	}

	public function getSources(){
		$sources = array();

		if (preg_match('/.*video\/.*/', $this->getMimetype()))
		{
			$fm = new FileManager();
			$sources = $fm->getSourcesByModel($this);
		}
		else
		{
			$sources[] = array($this->getName(), $this->getMimetype());
		}

		return $sources;

	}

}
