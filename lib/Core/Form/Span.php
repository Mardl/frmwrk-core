<?php
namespace Core\Form;

class Span extends Element{

	public function __construct()
	{ 
		
	}

	public function __toString(){
		$elements = '';
		foreach ($this->elements as $element){
			$elements .= $element;
		}
		
		$output = file_get_contents(APPLICATION_PATH.'/Layout/Form/span.html.php');
		$output = str_replace('{class}', $this->getCssClasses(), $output);
		$output = str_replace('{style}', $this->getInlineCss(), $output);
		$output = str_replace('{id}', $this->getId(), $output);
		$output = str_replace('{elements}',$elements, $output);
		$output = str_replace('{attr}', $this->renderAttributes(), $output);
		
		return $output;
	}
}