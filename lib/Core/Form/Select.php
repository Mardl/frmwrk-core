<?php
namespace Core\Form;

class Select extends Element{

	private $options = array();
	private $optGroups = array();
	private $size = 1;
	private $multiselect = false;

	public function setName($name){
		$this->name = $name;
	}

	public function addOption($value, $tag, $selected = false){
		$this->options[] = array($value,$tag,$selected);
	}

	public function addOptionGrouped($value, $tag, $optgroup, $selected = false){
		$this->options = array();

		if (!isset($this->optGroups[$optgroup]))
		{
			$this->optGroups[$optgroup] = array();
		}

		$this->optGroups[$optgroup][] = array($value,$tag,$selected);
	}

	public function setSize($size){
		$this->size = $size;
	}

	public function setMultiSelect($boolean){
		$this->multiselect = $boolean;
	}

	public function __toString(){
		$output = '';

		if (empty($this->options) && empty($this->optGroups)){
			return "Form element 'select' has no options.";
		}

		if (!is_null($this->getLabel())){
			$output .= $this->getLabel();
		}

		$output .= '<select name="'.$this->getName().'" '.$this->getId();

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
			if ($option[2] || ($this->getValue() == $option[0])){
				$output .= 'selected="selected"';
			}
			$output .= '>'.$option[1].'</option>';
		}

		foreach ($this->optGroups as $group => $options){
			$output .= "<optgroup label='".$group."'>";
			foreach ($options as $option)
			{
				$output .= '<option value="'.$option[0].'"';
				if ($option[2] || ($this->getValue() == $option[0])){
					$output .= 'selected="selected"';
				}
				$output .= '>'.$option[1].'</option>';
			}
			$output .= "</optgroup>";
		}

		$output .= '</select>';

		if ($this->breakafter){$output .= '<br/>';}

		return $output;
	}

}