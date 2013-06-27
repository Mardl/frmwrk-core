<?php
namespace Core\Html;

class Link extends Element
{

	private $path;
	private $nameenabled = true;
	private $target = '_self';
	private $title;

	private $renderOutput = '<a href="{href}" class="{class}" style="{style}" {id} {attr}>{name}</a>{breakafter}';

	public function __construct($id = null, $css = array(), $breakafter = false)
	{
		parent::__construct($id, $css, $breakafter);
		if (file_exists(APPLICATION_PATH . '/Layout/Html/anchor.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH . '/Layout/Html/anchor.html.php');
		}

	}

	public function setPath($path)
	{
		$this->path = $path;

	}

	public function setHref($href)
	{
		$this->setPath($href);
	}

	public function setImageAsName(Img $img)
	{
		$this->name = $img;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function setTarget($target)
	{
		$this->target = $target;
	}


	public function __toString()
	{
		$output = $this->renderStandard($this->renderOutput);

		$output = str_replace('{href}', $this->path, $output);
		$output = str_replace('{name}', $this->getName(), $output);

		return $output;

	}

}

?>