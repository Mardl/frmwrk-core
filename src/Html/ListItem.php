<?php

namespace Core\Html;

/**
 * Class ListItem
 *
 * @category Core
 * @package  Core\Html
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class ListItem extends Element
{

	private $renderOutput = '<li class="{class}" style="{style}" {id} {attr} >{elements}</li>';

	/**
	 * @param int   $id
	 * @param array $css
	 * @param bool  $breakafter
	 */
	public function __construct($id, $css = array(), $breakafter = false)
	{
		parent::__construct($id, $css, $breakafter);
		if (file_exists(APPLICATION_PATH . '/Layout/Html/li.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH . '/Layout/Html/li.html.php');
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
