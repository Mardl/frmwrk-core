<?php
namespace Core\Form;

class Text extends Element{

	private $text;
	
	public function __construct($id, $text = null, $css = array(), $breakafter = false){
		parent::__construct($id, $css, $breakafter);
		
		$this->breakafter = $breakafter;
		$this->text = $text;
	}
	
	public function setText($text){
		$this->text = $text;
		
	}
	
	public function addText($text){
		$this->text .= $text;
	}
	
	
	public function __toString(){
		$output = '';
		
		$output .= $this->text;
		if ($this->breakafter){$output .= '<br/>';}
		
		return $output;		
		
	}
	
}
?>