<?php
namespace Core\Form\Input;

use Core\Form\Input;

class Button extends Input{

	public function __toString(){
		
	if (is_null($this->name) && is_null($this->id)){
			throw new \ErrorException("Form element 'input' has neither id nor name.");
		}
		
		$output = file_get_contents(APPLICATION_PATH.'/Layout/Form/button.html.php');
		$output = str_replace('{class}', $this->getCssClasses(), $output);
		$output = str_replace('{type}', $this->type, $output);
		$output = str_replace('{style}', $this->getInlineCss(), $output);
		
		$output = str_replace('{id}', $this->getId(), $output);
		$output = str_replace('{name}', $this->getName(), $output);
		$output = str_replace('{label}', $this->getLabel(), $output);
		
		
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