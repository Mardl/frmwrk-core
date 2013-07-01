<?php

namespace Core\Html\Input;

use Core\Html\Input;

/**
 * Class Radio
 *
 * @category Core
 * @package  Core\Html\Input
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Radio extends Input
{

	private $options = array();
	private $renderOutput = '<label class="checkbox {class}"><input type="radio" class="{class}" style="{style}" {id} name="{name}" value="{value}" {attr} {checked}/> {title}</label>';

	/**
	 * Konstruktor
	 *
	 * @param int   $id
	 * @param array $default
	 * @param array $css
	 * @param bool  $breakafter
	 * @param array $opt
	 * @param bool  $required
	 */
	public function __construct($id, $default, $css = array(), $breakafter = false, $opt = array(), $required = false)
	{
		parent::__construct($id, $default, $css, $breakafter, $required);

		if (file_exists(APPLICATION_PATH . '/Layout/Html/radio.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH . '/Layout/Html/radio.html.php');
		}

		if (!empty($opt))
		{
			foreach ($opt as $key => $value)
			{
				$this->addOption($key, $value, $key == $default);
			}
		}
	}

	/**
	 * @param string $value
	 * @param string $tag
	 * @param bool   $selected
	 * @return void
	 */
	public function addOption($value, $tag, $selected = false)
	{
		$this->options[] = array($value, $tag, $selected);
	}

	/**
	 * @return bool|string
	 */
	public function validate()
	{
		if ($this->isRequired())
		{
			$found = false;
			foreach ($this->options as $option)
			{
				$found = $found || $option[2];
			}
			if (!$found)
			{
				return "Fehlende Eingabe für " . $this->getId();
			}
		}

		return true;
	}

	/**
	 * @return mixed|string
	 */
	public function __toString()
	{
		$output = '';

		$count = 0;
		foreach ($this->options as $option)
		{
			$count++;
			$opt = $this->renderOutput;
			$opt = str_replace('{id}', $this->getId($count), $opt);
			$opt = str_replace('{name}', $this->getName(), $opt);
			$opt = str_replace('{label}', $this->getLabel(), $opt);
			$opt = str_replace('{value}', htmlspecialchars($option[0]), $opt);
			$opt = str_replace('{title}', htmlspecialchars($option[1]), $opt);

			$opt = $this->renderStandard($opt);

			$opt = $option[2] ? str_replace('{checked}', 'checked="checked"', $opt) : str_replace('{checked}', '', $opt);

			$output .= $opt;
		}

		return $output;
	}
}
