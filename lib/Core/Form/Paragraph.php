<?php
namespace Core\Form;

class Paragraph extends Element
{

	public function __construct($breakafter=false){
		$this->breakafter = $breakafter;
	}

	public function __toString(){
		$elements = '';
		foreach ($this->elements as $element){
			$elements .= $element;
		}
		
		$output = file_get_contents(APPLICATION_PATH.'/Layout/Form/paragraph.html.php');
		$output = str_replace('{class}', $this->getCssClasses(), $output);
		$output = str_replace('{style}', $this->getInlineCss(), $output);
		$output = str_replace('{id}', $this->getId(), $output);
		$output = str_replace('{elements}',$elements, $output);
		
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