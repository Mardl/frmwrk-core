<?php
namespace Core\Form\Input;

use Core\Form\Input;

class Checkbox extends Input{
	
	private $title = null;
	
	public function setTitle($title){
		$this->title = $title;
	}
	
	private function getTitle(){
		return $this->title;
	}
	
	public function __toString(){
		
		$output = file_get_contents(APPLICATION_PATH.'/Layout/Form/checkbox.html.php');
		$output = str_replace('{class}', $this->getCssClasses(), $output);
		$output = str_replace('{type}', $this->type, $output);
		$output = str_replace('{style}', $this->getInlineCss(), $output);
		
		if ($this->getId()){
			$output = str_replace('{id}', $this->getId(), $output);
		} else {
			$output = str_replace('{id}', $this->getName(), $output);
		}
				
		$output = str_replace('{name}', $this->getName(), $output);
		$output = str_replace('{title}', $this->getTitle(), $output);
		$output = str_replace('{attr}', $this->renderAttributes(), $output);
		
		$output = str_replace('{value}', htmlspecialchars($this->getValue()), $output);
		
		return $output;
	}
	
}