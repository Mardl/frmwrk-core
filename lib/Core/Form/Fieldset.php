<?php
namespace Core\Form;

class Fieldset extends Element{

	private $legend = null;
	
	public function __construct($legend = null, $breakafter=false){
		$this->setLegend($legend);
		$this->breakafter = $breakafter;
	}
	
	public function setLegend($legend){
		$this->legend = $legend;
		
	}
	
	public function __toString(){
		$elements = '';
		foreach ($this->elements as $element){
			$elements .= $element;
		}
		
		$output = file_get_contents(APPLICATION_PATH.'/Layout/Form/fieldset.html.php');
		$output = str_replace('{class}', $this->getCssClasses(), $output);
		$output = str_replace('{style}', $this->getInlineCss(), $output);
		$output = str_replace('{id}', $this->getId(), $output);
		$output = str_replace('{elements}',$elements, $output);
		
		if (!is_null($this->legend))
		{
			$output = str_replace('{legend}', "<legend>".$this->legend."</legend>", $output);
		}
		else
		{
			$output = str_replace('{legend}', $this->legend, $output);
		}
		
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
?>