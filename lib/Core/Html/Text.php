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
		$t = '';
		$t .= $this->text;

		if ($this->breakafter)
		{
			$t .= '<br/>';
		}

		return $t;

	}

}
?>