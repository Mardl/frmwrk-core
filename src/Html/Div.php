<?php

namespace Core\Html;

/**
 * Class Div
 *
 * @category Core
 * @package  Core\Html
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Div extends Element
{

	private $renderOutput = '<div class="{class}" style="{style}" {id} {attr} >{elements}</div>{breakafter}';

	/**
	 * @param string $id
	 * @param array  $css
	 * @param bool   $breakafter
	 */
	public function __construct($id = '', $css = array(), $breakafter = false)
	{
		parent::__construct($id, $css, $breakafter);

		if (file_exists(APPLICATION_PATH . '/Layout/Html/div.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH . '/Layout/Html/div.html.php');
		}
	}

	/**
	 * @return mixed
	 */
	public function __toString()
	{
		$output = $this->renderStandard($this->renderOutput);

		return $output;
	}
}
