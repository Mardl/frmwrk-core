<?php

namespace Core\Html;

/**
 * Class Img
 *
 * @category Core
 * @package  Core\Html
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Img extends Element
{

	private $src;
	private $alt = null;
	private $width = null;
	private $height = null;
	private $renderOutput = '<img src="{src}" class="{class}" style="{style}" {id} alt="{alt}" {attr} {width} {height} />{breakafter}';

	/**
	 * Konstruktor
	 *
	 * @param bool $breakafter
	 */
	public function __construct($breakafter = false)
	{
		$this->breakafter = $breakafter;
		if (file_exists(APPLICATION_PATH . '/Layout/Html/image.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH . '/Layout/Html/image.html.php');
		}
	}

	/**
	 * @param string $src
	 * @return void
	 */
	public function setSrc($src)
	{
		$this->src = $src;
	}

	/**
	 * @param string $src
	 * @return void
	 * @deprecated
	 */
	public function setPath($src)
	{
		$this->setSrc($src);
	}

	/**
	 * @param string $alt
	 * @return void
	 */
	public function setAlt($alt)
	{
		$this->alt = $alt;
	}

	/**
	 * @param int $width
	 * @return void
	 */
	public function setWidth($width)
	{
		$this->width = $width;
	}

	/**
	 * @param int $height
	 * @return void
	 */
	public function setHeight($height)
	{
		$this->height = $height;
	}

	/**
	 * @return mixed
	 */
	public function __toString()
	{
		$output = $this->renderStandard($this->renderOutput);

		$output = str_replace('{src}', $this->src, $output);
		$output = !empty($this->alt) ? str_replace('{alt}', $this->alt, $output) : str_replace('{alt}', $this->src, $output);
		$output = !empty($this->width) ? str_replace('{width}', 'width="' . $this->width . '"', $output) : str_replace('{width}', '', $output);
		$output = !empty($this->height) ? str_replace('{height}', 'height="' . $this->height . '"', $output) : str_replace('{height}', '', $output);

		return $output;
	}
}