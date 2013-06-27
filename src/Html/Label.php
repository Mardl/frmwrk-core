<?php
namespace Core\Html;

class Label extends Element
{

	private $parent = null;

	private $renderOutput = '<label for="{parent}" class="{class}" style="{style}" {id} {attr}>{value}</label>{breakafter}';

	public function __construct($value = null, $parent = null, $breakafter = false)
	{
		$this->setValue($value);
		$this->parent = $parent;
		$this->breakafter = $breakafter;

		if (file_exists(APPLICATION_PATH . '/Layout/Html/anchor.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH . '/Layout/Html/anchor.html.php');
		}
	}

	public function setParent($parent)
	{
		$this->parent = $parent;

	}

	public function setValue($value)
	{
		$this->value = $value;

	}

	public function getValue()
	{
		return $this->value;

	}

	public function __toString()
	{
		if (is_null($this->value))
		{
			return "Label for '" . $this->parent . "' has no value";
		}

		$output = $this->renderStandard($this->renderOutput);
		$output = str_replace('{parent}', $this->parent, $output);
		$output = str_replace('{value}', $this->renderValue(), $output);

		return $output;

	}

	private function renderValue()
	{
		$val = $this->value;

		if ($this->isRequired())
		{
			$val .= ' *';
		}

		return $val;
	}

}

?>