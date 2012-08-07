<?php
namespace Core\Form;

class Img extends Element{

	private $path;
	private $alt;
	
	public function __construct($breakafter=false){
		$this->breakafter = $breakafter;
	}
	
	public function setPath($path){
		$this->path = $path;
		
	}
	
	public function setAlt($alt){
		$this->alt = $alt;
	}
		
	
	public function __toString(){
		$output = '<img src="'.$this->path.'"';
		
		if ($this->hasCssClasses()){
			$output .= ' class="'.$this->getCssClasses().'"';
		}
		
		if ($this->hasInlineCss()){
			$output .= ' style="'.$this->getInlineCss().'"';
		}
		
		
		if (!is_null($this->getId())){
			$output .= $this->getId();
		}
		
		$output .= ' alt="'.$this->alt.'" />';
		
		if ($this->breakafter){$output .= '<br/>';}
		
		return $output;		
		
	}
	
}
?>