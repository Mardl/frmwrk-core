<?php
namespace Core\Form;

class Textarea extends Element{
	
	public function __construct($name=null, $breakafter=false){
		$this->name = $name;		
		$this->breakafter = $breakafter;
		if ($this->breakafter){$this->setInlineStyle('clear', 'both');}
	}
	
	public function setDefault($default){
		$this->default = $default;
		
	}
	
	public function __toString(){
		$output = '';
		
		if (is_null($this->name)){
			return "Form element 'input' has no name.";
		}
	
		if (!is_null($this->getLabel())){
			$output .= $this->getLabel();
		}
		
		$output .= '<textarea name="'.$this->name.'" id="'.$this->name.'"';
		
		if ($this->hasCssClasses()){
			$output .= ' class="'.$this->getCssClasses().'"';
		}
		
		if ($this->hasInlineCss()){
			$output .= ' style="'.$this->getInlineCss().'"';
		}
				
		if (!is_null($this->jscript)){
			$output .= ' '.$this->jscript[0].'="'.$this->jscript[1].'"';
		}
		
		$output .= ' >';
	
		if (!is_null($this->default)){
			$output .= htmlspecialchars($this->default);
		}
		$output .= '</textarea>';
		
		if ($this->breakafter){$output .= '<br/>';}
	
		return $output;
	}
	
}