<?php
namespace Core\Form;

use Core\Form\Element;

class Label extends Element{

	private $parent = null;
	
	public function __construct($value=null, $parent=null, $breakafter=false){
		$this->setValue($value);
		$this->parent = $parent;
		$this->breakafter = $breakafter;
		
	}
	
	public function setParent($parent){
		$this->parent = $parent;
		
	}
	
	public function __toString(){
		
		if (is_null($this->value)){
			return "Label for '".$this->parent."' has no value";	
		}
		
		$output = '<label';
		
		if (!is_null($this->parent)){
			$output .= ' for="'.$this->parent.'"';
		}
		
		if ($this->hasCssClasses()){
			$output .= ' class="'.$this->getCssClasses().'"';
		}
		
		if ($this->hasInlineCss()){
			$output .= ' style="'.$this->getInlineCss().'"';
		}
		
		if ($this->isRequired())
		{
			$output .= '>'.$this->value.' *</label>';
		}
		else
		{
			$output .= '>'.$this->value.'</label>';
		}
		
		
		if ($this->breakafter){$output .= '<br/>';}
		
		return $output;		
		
	}
	
}
?>