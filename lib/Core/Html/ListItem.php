<?php
namespace Core\Html;

class ListItem extends Element{

	private $renderOutput = '<li class="{class}" style="{style}" {id} {attr} >{elements}</li>';

	public function __construct($id, $css = array(), $breakafter = false)
	{
		parent::__construct($id, $css, $breakafter);
		if (file_exists(APPLICATION_PATH.'/Layout/Form/li.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH.'/Layout/Form/li.html.php');
		}
	}

	public function __toString()
	{
		$output = $this->renderStandard($this->renderOutput);

		return $output;

	}

}
?>