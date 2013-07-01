<?php

namespace Core\Html;

/**
 * Class Link
 *
 * @category Core
 * @package  Core\Html
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Link extends Element
{

	private $path;
	private $nameenabled = true;
	private $target = '_self';
	private $title;

	private $renderOutput = '<a href="{href}" class="{class}" style="{style}" {id} {attr}>{name}</a>{breakafter}';

	/**
	 * @param int   $id
	 * @param array $css
	 * @param bool  $breakafter
	 */
	public function __construct($id = null, $css = array(), $breakafter = false)
	{
		parent::__construct($id, $css, $breakafter);

		if (file_exists(APPLICATION_PATH . '/Layout/Html/anchor.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH . '/Layout/Html/anchor.html.php');
		}
	}

	/**
	 * @param string $path
	 * @return void
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}

	/**
	 * @param string $href
	 * @return void
	 */
	public function setHref($href)
	{
		$this->setPath($href);
	}

	/**
	 * @param Img $img
	 * @return void
	 */
	public function setImageAsName(Img $img)
	{
		$this->name = $img;
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
	 * @param string $target
	 * @return void
	 */
	public function setTarget($target)
	{
		$this->target = $target;
	}

	/**
	 * @return mixed
	 */
	public function __toString()
	{
		$output = $this->renderStandard($this->renderOutput);

		$output = str_replace('{href}', $this->path, $output);
		$output = str_replace('{name}', $this->getName(), $output);

		return $output;
	}
}
