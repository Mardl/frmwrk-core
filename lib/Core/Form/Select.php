<?php
namespace Core\Form;

class Select extends Element{

	private $options = array();
	private $size = 1;
	private $multiselect = false;
	
	
	public function setName($name){
		$this->name = $name;
		
	}

	public function addOption($value, $tag, $selected = false){
		$this->options[] = array($value,$tag,$selected);
		
	}
	
	public function setSize($size){
		$this->size = $size;
		
	}
	
	public function setMultiSelect($boolean){
		$this->multiselect = $boolean;
		
	}
	
	public function __toString(){
		$output = '';
		
		if (empty($this->options)){
			return "Form element 'select' has no options.";
		}
		
		if (!is_null($this->getLabel())){
			$output .= $this->getLabel();
		}
		
		$output .= '<select name="'.$this->getName().'" id="'.$this->getId().'"';
		
		if ($this->size > 1){
			$output .= ' size="'.$this->size.'"';
		}
		
		if ($this->hasCssClasses()){
			$output .= ' class="'.$this->getCssClasses().'"';
		}
		
		if ($this->hasInlineCss()){
			$output .= ' style="'.$this->getInlineCss().'"';
		}
		
		if ($this->multiselect){
			$output .= ' multiple="multiple"';
		}
		
		$output .= ' >';
		
		
		foreach ($this->options as $option){
			$output .= '<option value="'.$option[0].'"';
			if ($option[2]){
				$output .= 'selected="selected"';
			}
			$output .= '>'.$option[1].'</option>';
		}
		
		
		$output .= '</select>';
		
		if ($this->breakafter){$output .= '<br/>';}
	
		return $output;
	}
	
}