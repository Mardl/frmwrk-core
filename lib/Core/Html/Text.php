<?php
namespace Core\Html;

class Text extends Element
{
	private $text = '';

	public function __construct($id, $text = '', $breakafter = false)
	{
		parent::__construct($id, array(), $breakafter);

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
		if ($this->breakafter)
		{
			$this->text .= '<br/>';
		}

		return $this->text;

	}

}
?>