<?php

namespace Core\Html\Input;

/**
 * Class Textarea
 *
 * @category Core
 * @package  Core\Html\Input
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Textarea extends \Core\Html\Input
{

	private $renderOutput = '{label}<textarea class="{class}" style="{style}" {id} name="{name}" {attr} />{value}</textarea>';

	/**
	 * Konstruktor
	 *
	 * @param int   $id
	 * @param array $default
	 * @param array $css
	 * @param bool  $breakafter
	 * @param bool  $required
	 */
	public function __construct($id, $default, $css = array(), $breakafter = false, $required = false)
	{
		parent::__construct($id, $default, $css, $breakafter, $required);

		if (file_exists(APPLICATION_PATH . '/Layout/Html/textarea.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH . '/Layout/Html/textarea.html.php');
		}

		$this->setValue($default);
	}

	/**
	 * @return mixed
	 */
	public function __toString()
	{
		$output = $this->renderStandard($this->renderOutput);

		$output = str_replace('{name}', $this->getName(), $output);
		$output = str_replace('{label}', $this->getLabel(), $output);
		$output = str_replace('{value}', htmlspecialchars($this->getValue()), $output);

		return $output;
	}
}
