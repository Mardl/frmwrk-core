<?php

namespace Core\Html\Input;

use Core\Html\Input;

/**
 * Class Button
 *
 * @category Core
 * @package  Core\Html\Input
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Button extends Input
{

	private $renderOutput = '<button type="{type}" class="{class}" style="{style}"  {id} name="{name}">{value}</button>{breakafter}';

	/**
	 * @param int   $id
	 * @param array $default
	 * @param array $css
	 * @param bool  $breakafter
	 */
	public function __construct($id, $default, $css = array(), $breakafter = false)
	{
		parent::__construct($id, $default, $css, $breakafter);

		if (file_exists(APPLICATION_PATH . '/Layout/Html/button.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH . '/Layout/Html/button.html.php');
		}
	}

	/**
	 * @return mixed
	 * @throws \ErrorException
	 */
	public function __toString()
	{
		if (is_null($this->name) && is_null($this->id))
		{
			throw new \ErrorException("Form element 'input' has neither id nor name.");
		}

		$output = $this->renderStandard($this->renderOutput);

		$output = str_replace('{type}', $this->type, $output);
		$output = str_replace('{name}', $this->getName(), $output);
		$output = str_replace('{value}', htmlspecialchars($this->getValue()), $output);

		return $output;
	}
}