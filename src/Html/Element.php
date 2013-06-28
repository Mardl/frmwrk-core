<?php

namespace Core\Html;

/**
 * Class Element
 *
 * @category Core
 * @package  Core\Html
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
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

	/**
	 * @param int   $id
	 * @param array $css
	 * @param bool  $breakafter
	 */
	public function __construct($id, $css = array(), $breakafter = false)
	{
		$this->id = $id;
		$this->addCssClasses($css);
		$this->breakafter = $breakafter;

	}

	/**
	 * @param Element $element
	 * @return void
	 */
	public function addElement(Element $element)
	{
		$this->elements[] = $element;
	}

	/**
	 * @param array $elements
	 * @return void
	 */
	public function addElements(array $elements)
	{
		foreach ($elements as $element)
		{
			$this->addElement($element);
		}
	}

	/**
	 * @param bool $required
	 * @return void
	 */
	public function setRequired($required = true)
	{
		$this->required = $required;
		if ($this->label)
		{
			$this->label->setRequired($required);
		}
	}

	/**
	 * @return bool
	 */
	public function isRequired()
	{
		return $this->required;
	}

	/**
	 * @param string $class
	 * @return void
	 */
	public function setCssClass($class)
	{
		$this->cssClasses = array($class);
	}

	/**
	 * @param string $class
	 * @return void
	 */
	public function addCssClass($class)
	{
		$this->cssClasses[] = $class;
	}

	/**
	 * @param array $classes
	 * @return void
	 */
	public function addCssClasses(array $classes)
	{
		foreach ($classes as $class)
		{
			$this->addCssClass($class);
		}
	}

	/**
	 * @return bool
	 */
	public function hasCssClasses()
	{
		return (count($this->cssClasses) >= 1);
	}

	/**
	 * @return string
	 */
	public function getCssClasses()
	{
		$output = implode(' ', $this->cssClasses);

		return $output;
	}

	/**
	 * @param string $style
	 * @param string $value
	 * @return void
	 */
	public function setInlineStyle($style, $value)
	{
		$this->cssInline[$style] = $value;
	}

	/**
	 * @return string
	 */
	public function getInlineCss()
	{
		$output = '';
		foreach ($this->cssInline as $style => $value)
		{
			$output .= $style . ':' . $value . ';';

		}

		return $output;
	}

	/**
	 * @return bool
	 */
	public function hasInlineCss()
	{
		return (count($this->cssInline) >= 1);
	}

	/**
	 * @param string $label
	 * @return void
	 */
	public function setLabel($label)
	{
		if (!($label instanceof Label))
		{
			$label = new Label($label, $this->getName());
		}
		$this->label = $label;
	}

	/**
	 * @return null
	 */
	public function getLabel()
	{
		return $this->label;
	}

	/**
	 * @param int $id
	 * @return void
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return int|null
	 */
	public function getPlainId()
	{
		return $this->id;
	}

	/**
	 * @param string $count
	 * @return null|string
	 */
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

	/**
	 * @param string $name
	 * @return void
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return int|null
	 */
	public function getName()
	{
		if (!is_null($this->name))
		{
			return $this->name;
		}

		return $this->id;
	}

	/**
	 * @return array
	 */
	public function getElements()
	{
		return $this->elements;
	}

	/**
	 * @return bool
	 */
	public function hasElements()
	{
		return (count($this->elements) > 0);
	}

	/**
	 * @return bool|string
	 */
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

	/**
	 * @param string $name
	 * @param string $value
	 * @return void
	 */
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

	/**
	 * @param string $name
	 * @return bool
	 */
	public function hasAttribute($name)
	{
		return array_key_exists($name, $this->attributes);
	}

	/**
	 * @return string
	 */
	public function renderAttributes()
	{
		$output = '';

		foreach ($this->attributes as $attr => $vals)
		{
			$output .= $attr . "='" . implode(' ', $vals) . "' ";
		}

		return $output;
	}

	/**
	 * @param bool $readonly
	 * @return void
	 */
	public function setReadonly($readonly)
	{
		$this->readonly = $readonly;
	}

	/**
	 * @return bool
	 */
	public function getReadonly()
	{
		return $this->readonly;
	}

	/**
	 * @param string $output
	 * @return string
	 */
	protected function renderCssClasses($output)
	{
		return $this->getCssClasses();
	}

	/**
	 * @param string $output
	 * @return string
	 */
	protected function renderInlineStyles($output)
	{
		return $this->getInlineCss();
	}

	/**
	 * @param string $output
	 * @return mixed
	 */
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

	/**
	 * @param string $data
	 * @return mixed
	 */
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
