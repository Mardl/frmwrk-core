<?php

namespace Core;

/**
 * Class Form
 *
 * @category Core
 * @package  Core
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Form
{

	private $method = 'post';
	private $class = '';
	private $action = null;
	private $id = 'formular';
	private $captcha = false;
	private $attributes = array();

	protected $elements = array();
	protected $values = array();

	/**
	 * @param array $data
	 */
	public function __construct($data = array())
	{

		$this->values = $data;
	}

	/**
	 * @param string $key
	 * @param null   $default
	 * @return null
	 */
	public function getValue($key, $default = null)
	{
		if (array_key_exists($key, $this->values))
		{
			return $this->values[$key];
		}

		return $default;
	}

	/**
	 * @param string $method
	 * @return void
	 */
	public function setMethod($method)
	{
		$this->method = $method;
	}

	/**
	 * @param string $action
	 * @return void
	 */
	public function setAction($action)
	{
		$this->action = $action;
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
	 * @param string $class
	 * @return void
	 */
	public function setClass($class)
	{
		$this->class = $class;
	}

	/**
	 * @return string
	 */
	public function getClass()
	{
		return $this->class;
	}

	/**
	 * @return null|string
	 */
	public function getId()
	{
		if (!empty($this->id))
		{
			return "id='" . $this->id . "'";
		}

		return null;
	}

	/**
	 * @param array $attr
	 * @return void
	 */
	public function addAttribute(array $attr)
	{
		$this->attributes[] = $attr;

	}

	/**
	 * @param Html\Element $element
	 * @return void
	 */
	public function addElement(Html\Element $element)
	{
		$this->elements[] = $element;
	}

	/**
	 * @return bool
	 */
	public function hasElements()
	{
		return (count($this->elements) > 0);
	}

	/**
	 * @return array
	 */
	public function getElements()
	{
		return $this->elements;
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
	 * @param array $elements
	 * @return void
	 */
	public function addElementsAllTypes($elements)
	{
		if (is_array($elements))
		{
			$this->addElements($elements);
		}
		elseif ($elements instanceof \Core\Html\Element)
		{
			$this->addElement($elements);
		}
		else
		{
			$text = new \Core\Html\Text('', $elements);
			$this->addElement($text);
		}
	}

	/**
	 * @return mixed
	 */
	public function __toString()
	{
		$elements = '';
		foreach ($this->elements as $element)
		{
			$elements .= $element;
		}

		$attributes = '';
		foreach ($this->attributes as $attr)
		{
			$attributes = $attr[0] . '="' . $attr[1] . '" ';
		}

		$output = '<form method="{method}" action="{action}" {id} {attributes}>{elements}</form>';

		if (file_exists(APPLICATION_PATH . '/Layout/Html/form.html.php'))
		{
			$output = file_get_contents(APPLICATION_PATH . '/Layout/Html/form.html.php');
		}
		$output = str_replace('{method}', $this->method, $output);
		$output = str_replace('{class}', $this->class, $output);
		$output = str_replace('{action}', $this->action, $output);
		$output = str_replace('{id}', $this->getId(), $output);
		$output = str_replace('{attributes}', $attributes, $output);
		$output = str_replace('{elements}', $elements, $output);

		$output = $this->clearUp($output);

		return $output;
	}

	/**
	 * @param array $data
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

	/**
	 * @param int   $elementId
	 * @param mixed $value
	 * @param mixed $updateType
	 * @param null  $container
	 * @return void
	 */
	public function updateElement($elementId, $value, $updateType, $container = null)
	{
		if (is_null($container))
		{
			$container = $this;
		}

		foreach ($container->getElements() as $el)
		{
			if ($el->getPlainId() == $elementId)
			{
				$el->$updateType($value);

				return;
			}

			if ($el->hasElements())
			{
				$this->updateElement($elementId, $value, $updateType, $el);
			}
		}
	}

	/**
	 * @param int  $elementId
	 * @param null $container
	 * @return null
	 */
	public function findElement($elementId, $container = null)
	{
		$target = null;

		if (is_null($container))
		{
			$container = $this;
		}

		foreach ($container->getElements() as $el)
		{
			if ($el->getPlainId() == $elementId)
			{
				return $el;
			}

			if ($el->hasElements())
			{
				$target = $this->findElement($elementId, $el);
			}
		}

		return $target;
	}
}
