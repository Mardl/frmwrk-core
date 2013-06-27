<?php
namespace Core\Html;

class Paragraph extends Element
{

	private $renderOutput = '<p class="{class}" style="{style}" {id}>{elements}</p>{breakafter}';

	public function __construct($breakafter = false)
	{
		$this->breakafter = $breakafter;
		if (file_exists(APPLICATION_PATH . '/Layout/Html/paragraph.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH . '/Layout/Html/paragraph.html.php');
		}

	}


	public function __toString()
	{
		$output = $this->renderStandard($this->renderOutput);

		return $output;

	}

}

?>