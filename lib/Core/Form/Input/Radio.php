<?php
namespace Core\Form\Input;

use Core\Form\Input;

class Radio extends Input{

	private $options = array();
	
	public function addOption($value, $tag, $selected = false){
		$this->options[] = array($value,$tag,$selected);
		
	}
	
	public function __toString(){
		$output = '';
		
		$option = file_get_contents(APPLICATION_PATH.'/Layout/Form/radio.html.php');
		
		foreach ($this->options as $option){
			
			if ($this->hasCssClasses()){
				$option = str_replace('{class}', 'class="'.$this->getCssClasses().'"', $option);
			}
			else
			{
				$option = str_replace('{class}', '', $option);
			}
			
			$option = str_replace('{style}', $this->getInlineCss(), $option);
			
			if ($this->getId()){
				$option = str_replace('{id}', $this->getId(), $option);
			} else {
				$option = str_replace('{id}', $this->name, $option);
			}
			
			$option = str_replace('{name}', $this->getName(), $option);
			$option = str_replace('{label}', $this->getLabel(), $option);
			$option = str_replace('{attr}', $this->renderAttributes(), $option);
			
			$option = str_replace('{value}', htmlspecialchars($option[0]), $option);
			$option = str_replace('{title}', htmlspecialchars($option[1]), $option);
			
			if ($this->breakafter)
			{
				$option = str_replace('{breakafter}', '<br/>', $option);
			}
			else
			{
				$option = str_replace('{breakafter}', null, $option);
			}
			
			$output .= $option;
		}
			
		return $output;
	}
	
	
}