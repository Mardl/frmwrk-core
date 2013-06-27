<?php
namespace Core\Html\Input;

use Core\Html\Input;

class Checkbox extends Input
{

	private $title = null;
	private $renderOutput = '<label class="checkbox {class}"><input type="checkbox" class="{class}" style="{style}" {id} name="{name}" value="{value}" {attr} /> {title} </label> ';

	public function __construct($id, $default, $css = array(), $breakafter = false, $postValue = '', $required = false)
	{
		parent::__construct($id, $default, $css, $breakafter, $required);

		if (file_exists(APPLICATION_PATH . '/Layout/Html/checkbox.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH . '/Layout/Html/checkbox.html.php');
		}

		if ($default == $postValue)
		{
			$this->addAttribute('checked', 'checked');
		}


	}

	public function setTitle($title)
	{
		$this->setLabel($title);
		$this->title = $title;
	}

	public function getTitle()
	{
		$output = $this->title;
		if ($this->isRequired())
		{
			$output = $this->title . ' *';
		}

		return $output;
	}

	public function validate()
	{
		if ($this->isRequired() && !$this->hasAttribute('checked'))
		{
			if ($this->label)
			{
				return "Fehlende Eingabe für " . $this->label->getValue();
			}
			else
			{
				return "Fehlende Eingabe für " . $this->getId();
			}
		}

		return true;
	}

	public function __toString()
	{

		$output = $this->renderStandard($this->renderOutput);

		$output = str_replace('{name}', $this->getName(), $output);
		$output = str_replace('{value}', htmlspecialchars($this->getValue()), $output);
		$output = str_replace('{label}', $this->getLabel(), $output);
		$output = str_replace('{title}', $this->getTitle(), $output);

		return $output;
	}
}