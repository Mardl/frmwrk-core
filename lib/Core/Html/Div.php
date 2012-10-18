<?php
namespace Core\Html;

class Div extends Element{

	private $renderOutput = '<div class="{class}" style="{style}" {id} {attr} >{elements}</div>{breakafter}';

	public function __construct($id='',$css = array(), $breakafter=false)
	{
		parent::__construct($id , $css, $breakafter);

		if (file_exists(APPLICATION_PATH.'/Layout/Form/div.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH.'/Layout/Form/div.html.php');
		}
	}

	public function __toString()
	{
		$output = $this->renderStandard($this->renderOutput);
		return $output;
	}


}
?>