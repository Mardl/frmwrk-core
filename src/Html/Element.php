<?php
namespace Core\Html;

class Element
{

	protected $required = false;
	protected $cssClasses = array();
	protected $cssInline = array();
	protected $attributes = array();
	protected $label = null;
	protected $id = null;
	protected $name = null;
	protected $value = null;
	protected $elements = array();
	protected $breakafter = false;
	protected $readonly = false;

	public function __construct($id, $css = array(), $breakafter = false)
	{
		$this->id = $id;
		$this->addCssClasses($css);
		$this->breakafter = $breakafter;

	}

	public function addElement(Element $element)
	{
		$this->elements[] = $element;

	}

	public function addElements(array $elements)
	{
		foreach ($elements as $element)
		{
			$this->addElement($element);
		}

	}

	public function setRequired($required = true)
	{
		$this->required = $required;
		if ($this->label)
		{
			$this->label->setRequired($required);
		}
	}

	public function isRequired()
	{
		return $this->required;

	}

	public function setCssClass($class)
	{
		$this->cssClasses = array($class);

	}

	public function addCssClass($class)
	{
		$this->cssClasses[] = $class;

	}

	public function addCssClasses(array $classes)
	{
		foreach ($classes as $class)
		{
			$this->addCssClass($class);
		}

	}

	public function hasCssClasses()
	{
		return (count($this->cssClasses) >= 1);

	}

	public function getCssClasses()
	{
		$output = implode(' ', $this->cssClasses);

		return $output;

	}

	public function setInlineStyle($style, $value)
	{
		$this->cssInline[$style] = $value;

	}

	public function getInlineCss()
	{
		$output = '';
		foreach ($this->cssInline as $style => $value)
		{
			$output .= $style . ':' . $value . ';';

		}

		return $output;

	}

	public function hasInlineCss()
	{
		return (count($this->cssInline) >= 1);

	}

	public function setLabel($label)
	{
		if (!($label instanceof Label))
		{
			$label = new Label($label, $this->getName());
		}
		$this->label = $label;

	}

	public function getLabel()
	{
		return $this->label;

	}

	public function setId($id)
	{
		$this->id = $id;

	}

	public function getPlainId()
	{
		return $this->id;

	}

	public function getId($count = '')
	{
		if (empty($this->id) && empty($this->name))
		{
			return null;
		}
		else
		{
			if (empty($this->id))
			{
				return ' id="' . $this->name . (!empty($count) ? '-' . $count : '') . '"';
			}

			return ' id="' . $this->id . (!empty($count) ? '-' . $count : '') . '"';
		}

	}

	public function setName($name)
	{
		$this->name = $name;

	}

	public function getName()
	{
		if (!is_null($this->name))
		{
			return $this->name;
		}

		return $this->id;
	}

	public function getElements()
	{
		return $this->elements;
	}

	public function hasElements()
	{
		return (count($this->elements) > 0);
	}

	public function validate()
	{
		if ($this->isRequired() && empty($this->value))
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

	public function addAttribute($name, $value)
	{
		if (array_key_exists($name, $this->attributes))
		{
			$this->attributes[$name][] = $value;
		}
		else
		{
			$this->attributes[$name] = array($value);
		}
	}

	public function hasAttribute($name)
	{
		return array_key_exists($name, $this->attributes);
	}

	public function renderAttributes()
	{
		$output = '';

		foreach ($this->attributes as $attr => $vals)
		{
			$output .= $attr . "='" . implode(' ', $vals) . "' ";
		}

		return $output;
	}

	public function setReadonly($readonly)
	{
		$this->readonly = $readonly;
	}

	public function getReadonly()
	{
		return $this->readonly;
	}

	protected function renderCssClasses($output)
	{
		return $this->getCssClasses();
	}

	protected function renderInlineStyles($output)
	{
		return $this->getInlineCss();
	}

	protected function renderStandard($output)
	{
		$elements = '';
		foreach ($this->elements as $element)
		{
			$elements .= $element;
		}

		$output = str_replace('{class}', $this->renderCssClasses($output), $output);
		$output = str_replace('{style}', $this->renderInlineStyles($output), $output);
		$output = str_replace('{id}', $this->getId(), $output);
		$output = str_replace('{attr}', $this->renderAttributes(), $output);
		$output = str_replace('{elements}', $elements, $output);
		$output = $this->breakafter ? str_replace('{breakafter}', '<br class="clear"/>', $output) : str_replace('{breakafter}', '', $output);

		$output = $this->clearUp($output);

		return $output;
	}


	private function clearUp($data)
	{
		$output = str_replace('class=""', '', $data);
		$output = str_replace("class=''", '', $output);
		$output = str_replace('id=""', '', $output);
		$output = str_replace("id=''", '', $output);
		$output = str_replace('style=""', '', $output);
		$output = str_replace("style=''", '', $output);

		return $output;
	}
}

?>