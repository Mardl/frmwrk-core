<?php
namespace Core\Html;

class Textarea extends Element{

	private $renderOutput = '{label}<textarea class="{class}" style="{style}" {id} name="{name}" {attr} />{value}</textarea>';

	public function __construct($id, $default, $css = array(), $breakafter = false){
		parent::__construct($id, $css, $breakafter);

		if (file_exists(APPLICATION_PATH.'/Layout/Form/input.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH.'/Layout/Form/textarea.html.php');
		}

		$this->setValue($default);

	}

	public function __toString(){

		$output = $this->renderStandard($this->renderOutput);

		$output = str_replace('{name}', $this->getName(), $output);
		$output = str_replace('{label}', $this->getLabel(), $output);
		$output = str_replace('{value}', htmlspecialchars($this->getValue()), $output);

		return $output;
	}

}