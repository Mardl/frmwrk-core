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
		
		$opt = file_get_contents(APPLICATION_PATH.'/Layout/Form/radio.html.php');
		
		foreach ($this->options as $option){
			
			if ($this->hasCssClasses()){
				$opt = str_replace('{class}', 'class="'.$this->getCssClasses().'"', $opt);
			}
			else
			{
				$opt = str_replace('{class}', '', $opt);
			}
			
			$opt = str_replace('{style}', $this->getInlineCss(), $opt);
			
			$opt = str_replace('{id}', $this->getId(), $opt);
			
			$opt = str_replace('{name}', $this->getName(), $opt);
			$opt = str_replace('{label}', $this->getLabel(), $opt);
			$opt = str_replace('{attr}', $this->renderAttributes(), $opt);
			
			$opt = str_replace('{value}', htmlspecialchars($option[0]), $opt);
			$opt = str_replace('{title}', htmlspecialchars($option[1]), $opt);
			
			if ($option[2])
			{
				$opt = str_replace('{checked}', 'checked="checked"', $opt);
			} 
			else 
			{
				$opt = str_replace('{checked}', '', $opt);
			}
			
			if ($this->breakafter)
			{
				$opt = str_replace('{breakafter}', '<br/>', $opt);
			}
			else
			{
				$opt = str_replace('{breakafter}', null, $opt);
			}
			
			$output .= $opt;
		}
		
		return $output;
	}
	
	
}