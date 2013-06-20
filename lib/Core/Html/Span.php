<?php
namespace Core\Html;

class Span extends Element{

	private $renderOutput = '<span class="{class}" style="{style}" {id} {attr} >{elements}</span>{breakafter}';

	public function __construct($id = '', $css = array(), $breakafter = false)
	{
		parent::__construct($id, $css, $breakafter);
		if (file_exists(APPLICATION_PATH.'/Layout/Html/span.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH.'/Layout/Html/span.html.php');
		}
	}

	public function __toString()
	{
		$output = $this->renderStandard($this->renderOutput);
		return $output;
	}

}
?>