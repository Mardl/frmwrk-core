<?php
namespace Core\Html;

class Ul extends Element{

	private $listItems = array();

	public function addItem(ListItem $item){
		$this->listItems[] = $item;

	}

	public function __toString(){
		$output = '';

		if (empty($this->listItems)){
			return "Liste ohne Listenpunkte.";
		}

		if (!is_null($this->getLabel())){
			$output .= $this->getLabel();
		}

		$output .= '<ul '.$this->getId();

		if ($this->hasCssClasses()){
			$output .= ' class="'.$this->getCssClasses().'"';
		}

		if ($this->hasInlineCss()){
			$output .= ' style="'.$this->getInlineCss().'"';
		}

		$output .= ' >';

		foreach ($this->listItems as $item){
			$output .= $item;
		}



		$output .= '</ul>';

		if ($this->breakafter){$output .= '<br/>';}

		return $output;
	}

}