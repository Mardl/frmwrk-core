<?php

namespace Core\Html\Input;

/**
 * Class Select
 *
 * @category Core
 * @package  Core\Html\Input
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Select extends \Core\Html\Input
{

	private $options = array();
	private $optGroups = array();
	private $size = 1;
	private $multiselect = false;

	private $renderOutput = '{label}<select class="{class}" style="{style}" {id} name="{name}" {multiple} {size}>{options}</select>{breakafter}';

	/**
	 * Konstruktor
	 *
	 * @param string $id
	 * @param array  $css
	 * @param bool   $breakafter
	 * @param bool   $required
	 */
	public function __construct($id = '', $css = array(), $breakafter = false, $required = false)
	{
		parent::__construct($id, '', $css, $breakafter, $required);

		if (file_exists(APPLICATION_PATH . '/Layout/Html/select.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH . '/Layout/Html/select.html.php');
		}
	}

	/**
	 * @return bool|string
	 */
	public function validate()
	{
		if ($this->isRequired())
		{
			$found = false;
			$count = 0;

			foreach ($this->options as $option)
			{
				$count++;
				if ($count == 1)
				{
					continue;
				}
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
	 * @param string $name
	 * @return void
	 */
	public function setName($name)
	{
		$this->name = $name;
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
	 * @param string $value
	 * @param string $tag
	 * @param mixed  $optgroup
	 * @param bool   $selected
	 * @return void
	 */
	public function addOptionGrouped($value, $tag, $optgroup, $selected = false)
	{
		if (!isset($this->optGroups[$optgroup]))
		{
			$this->optGroups[$optgroup] = array();
		}

		$this->optGroups[$optgroup][] = array($value, $tag, $selected);
	}

	/**
	 * @param int $size
	 * @return void
	 */
	public function setSize($size)
	{
		$this->size = $size;
	}

	/**
	 * @param bool $boolean
	 * @return void
	 */
	public function setMultiSelect($boolean)
	{
		$this->multiselect = $boolean;
	}

	/**
	 * @return mixed|string
	 */
	public function __toString()
	{
		if (empty($this->options) && empty($this->optGroups))
		{
			return "Form element 'select' has no options.";
		}

		$output = $this->renderStandard($this->renderOutput);

		$output = str_replace('{name}', $this->getName(), $output);

		$output = str_replace('{label}', $this->getLabel(), $output);

		$output = str_replace('{size}', $this->renderSize(), $output);
		$output = str_replace('{multiple}', $this->renderMultiple(), $output);
		$output = str_replace('{options}', $this->renderOptions(), $output);
		$output = str_replace('{readonly}', $this->getReadonly() ? 'readonly' : '', $output);

		return $output;
	}

	/**
	 * @return string
	 */
	private function renderSize()
	{
		$size = '';
		if ($this->size > 1)
		{
			$size = ' size="' . $this->size . '"';
		}

		return $size;
	}

	/**
	 * @return string
	 */
	private function renderMultiple()
	{
		$multiple = '';
		if ($this->multiselect > 1)
		{
			$multiple = ' multiple="multiple"';
		}

		return $multiple;
	}

	/**
	 * @return string
	 */
	private function renderOptions()
	{
		$opts = '';
		foreach ($this->options as $option)
		{
			$opts .= '<option value="' . $option[0] . '"';
			if ($option[2] || ($this->getValue() == $option[0]))
			{
				$opts .= 'selected="selected"';
			}
			$opts .= '>' . $option[1] . '</option>';
		}

		foreach ($this->optGroups as $group => $options)
		{
			$opts .= "<optgroup label='" . $group . "'>";
			foreach ($options as $option)
			{
				$opts .= '<option value="' . $option[0] . '"';
				if ($option[2] || ($this->getValue() == $option[0]))
				{
					$opts .= 'selected="selected"';
				}
				$opts .= '>' . $option[1] . '</option>';
			}
			$opts .= "</optgroup>";
		}

		return $opts;
	}
}
