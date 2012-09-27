<?php
namespace Core\Form;

class Link extends Element{

	private $path;
	private $nameenabled = true;
	private $target = '_self';
	private $title;


	public function __construct($breakafter=false){
		$this->breakafter = $breakafter;
	}

	public function setPath($path){
		$this->path = $path;

	}

	public function setImageAsName(Img $img){
		$this->name = $img;
	}

	public function setTitle($title){
		$this->title = $title;
	}

	public function setTarget($target){
		$this->target = $target;
	}


	public function __toString(){
		$output = '<a href="'.$this->path.'"';

		if ($this->hasCssClasses()){
			$output .= ' class="'.$this->getCssClasses().'"';
		}

		if ($this->hasInlineCss()){
			$output .= ' style="'.$this->getInlineCss().'"';
		}
		/*
		if (!is_null($this->jscript)){
			$output .= ' '.$this->jscript[0].'="'.$this->jscript[1].'"';
		}
		*/
		if (!is_null($this->getId())){
			#$output .= $this->getId();
		}

		$output .= ' '.$this->renderAttributes();

		//$output .= ' title="'.$this->title.'" target="'.$this->target.'" >';
		$output .= ' >';

		$output .= $this->name;

		$output .= '</a>';

		if ($this->breakafter){$output .= '<br/>';}

		return $output;

	}

}
?>