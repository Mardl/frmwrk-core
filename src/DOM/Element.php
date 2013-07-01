<?php

namespace Core\DOM;

use DOMElement;

/**
 * Class Element
 *
 * @category Core
 * @package  Core\DOM
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Element extends DOMElement
{

	/**
	 * @param array $attributes
	 * @return void
	 */
	public function setAttributes($attributes)
	{
		foreach ($attributes as $key => $value)
		{
			$this->setAttribute($key, $value);
		}
	}

	/**
	 * @param string $name
	 * @param null   $value
	 * @param array  $attributes
	 * @return \DOMNode
	 */
	public function addElement($name, $value = null, $attributes = array())
	{
		return $this->appendChild($this->ownerDocument->createElement($name, $value, $attributes));
	}

	/**
	 * @param string $namespace
	 * @param string $name
	 * @param null   $value
	 * @param array  $attributes
	 * @return \DOMNode
	 */
	public function addElementNS($namespace, $name, $value = null, $attributes = array())
	{
		return $this->appendChild($this->ownerDocument->createElementNS($namespace, $name, $value, $attributes));
	}
}
