<?php
namespace Core\Html;

class Textarea extends Element{

	public function __construct($id, $default, $css = array(), $breakafter = false){
		parent::__construct($id, $css, $breakafter);
		$this->setValue($default);

	}

	public function __toString(){

		$output = file_get_contents(APPLICATION_PATH.'/Layout/Form/textarea.html.php');

		if ($this->hasCssClasses()){
			$output = str_replace('{class}', 'class="'.$this->getCssClasses().'"', $output);
		}
		else
		{
			$output = str_replace('{class}', '', $output);
		}

		$output = str_replace('{style}', $this->getInlineCss(), $output);

		$output = str_replace('{id}', $this->getId(), $output);

		$output = str_replace('{name}', $this->getName(), $output);
		$output = str_replace('{attr}', $this->renderAttributes(), $output);

		$output = str_replace('{value}', htmlspecialchars($this->getValue()), $output);

		if ($this->breakafter)
		{
			$output = str_replace('{breakafter}', '<br/>', $output);
		}
		else
		{
			$output = str_replace('{breakafter}', null, $output);
		}

		return $output;
	}

}