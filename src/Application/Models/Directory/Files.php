<?php

namespace Core\Application\Models\Directory;

use Core\Model as BaseModel, Core\Application\Manager\Directory\Files as FileManager;

/**
 * Class Files
 *
 * @category Core
 * @package  Core\Application\Models\Directory
 * @author   Alexander Jonser <alex@dreiwerken.de>
 *
 * @method string getOrgname()
 * @method string getName()
 * @method string getBasename()
 * @method \Directory getDirectory()
 * @method Files getParent()
 * @method string getMimetype()
 * @method float getSize()
 *
 * @method setOrgname($value)
 * @method setName($value)
 * @method setBasename($value)
 * @method setDirectory(\Core\Application\Manager\Directory $value)
 * @method setParent(Files $value)
 * @method setMimetype($value)
 * @method setSize(\float $value)
 *
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
	 * Basename
	 *
	 * @var string
	 *
	 * @Column(type="string", length=255)
	 */
	protected $basename;

	/**
	 * Directory
	 *
	 * @var \Core\Application\Models\Directory
	 *
	 * @ManyToOne(targetEntity="App\Models\Directory")
	 * @JoinColumn(name="directory_id", referencedColumnName="id", nullable=false)
	 */
	protected $directory;

	/**
	 * Parent
	 *
	 * @var \Core\Application\Models\Directory\Files
	 *
	 * @ManyToOne(targetEntity="App\Models\Directory\Files")
	 * @JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
	 */
	protected $parent;

	/**
	 * MIME-Type
	 *
	 * @var string
	 *
	 * @Column(type="string", length=255)
	 */
	protected $mimetype;

	/**
	 * Dateigröße
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
	 * @return int|string
	 */
	public function getParentIds()
	{
		if (!is_null($this->directory))
		{
			return ($this->directory->getParentIds() . ',' . $this->directory->getId());
		}

		return 0;
	}

	/**
	 * Liefert ein leeres Array. (Files haben keine Children)
	 *
	 * @return array
	 */
	public function getChildren()
	{
		return array();
	}

	/**
	 * @param int    $width
	 * @param int    $height
	 * @param string $alt
	 * @param string $style
	 * @param null   $additional
	 * @return mixed|string
	 */
	public function getThumbnail($width = 128, $height = 128, $alt = '', $style = 'margin: 0px 10px 10px 0px;', $additional = null)
	{
		if (is_null($this->name))
		{
			return;
		}

		if (!preg_match('/.*video\/.*/', $this->getMimetype()))
		{
			$fm = new FileManager();
			$thumb = $fm->getThumbnail($this, $width, $height);

			return "<img src='" . $thumb . "' alt='" . $alt . "' style='" . $style . "' " . $additional . " />";
		}
		else
		{
			$sources = $this->getSources();
			$visu = '';
			if (!empty($sources))
			{
				$visu = '<video height="' . $height . '" autoplay="autoplay" {poster}loop="loop" style="' . $style . '">';
				foreach ($sources as $source)
				{
					if (!preg_match('/.*video\/.*/', $source[1]))
					{
						$visu = str_replace("{poster}", 'poster="/files/' . $source[0] . '" ', $visu);
					}
					else
					{
						$visu .= '<source src="/files/' . $source[0] . '" type="' . $source[1] . '" style="' . $style . '" />';
					}
				}
				$visu = str_replace("{poster}", '', $visu);
				$visu .= 'Ihr Browser unterstützt den Video Tag nicht.';
				$visu .= '</video>';
			}

			return $visu;
		}
		//throw new \ErrorException('Bei Videos gibts kein Thumbnail!');
	}

	/**
	 * @param int $width
	 * @param int $height
	 * @return bool|string
	 */
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

	/**
	 * @return array
	 */
	public function getSources()
	{
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
