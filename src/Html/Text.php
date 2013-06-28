<?php

namespace Core\Html;

/**
 * Class Text
 *
 * @category Core
 * @package  Core\Html
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Text extends Element
{

	private $text = '';

	/**
	 * @param int       $id
	 * @param string    $text
	 * @param bool      $breakafter
	 */
	public function __construct($id, $text = '', $breakafter = false)
	{
		parent::__construct($id, array(), $breakafter);

		$this->breakafter = $breakafter;
		$this->text = $text;
	}

	/**
	 * @param string $text
	 * @return void
	 */
	public function setText($text)
	{
		$this->text = $text;
	}

	/**
	 * @param string $text
	 * @return void
	 */
	public function addText($text)
	{
		$this->text .= $text;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		$t = '';
		$t .= $this->text;

		if ($this->breakafter)
		{
			$t .= '<br/>';
		}

		return $t;
	}
}
