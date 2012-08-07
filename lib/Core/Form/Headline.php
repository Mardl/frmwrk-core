<?php
namespace Core\Form;

class Headline extends Element{

	protected $index = 1;
	
	public function __construct()
	{
	
	}
	
	public function __toString(){
		$elements = '';
		foreach ($this->elements as $element){
			$elements .= $element;
		}
	
		$output = file_get_contents(APPLICATION_PATH.'/Layout/Form/headline.html.php');
		$output = str_replace('{id}', $this->getId(), $output);
		$output = str_replace('{index}', $this->getIndex(), $output);
		$output = str_replace('{elements}',$elements, $output);
		$output = str_replace('{attr}', $this->renderAttributes(), $output);
	
		return $output;
	
	}
	
	public function setIndex($index)
	{
		$this->index = $index;
	}
	
	public function getIndex()
	{
		return $this->index;
	}
	
	
}
?>