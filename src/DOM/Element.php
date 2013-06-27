<?php

namespace Core\DOM;

use DOMElement;

class Element extends DOMElement
{

	public function setAttributes($attributes)
	{
		foreach ($attributes as $key => $value)
		{
			$this->setAttribute($key, $value);
		}
	}

	public function addElement($name, $value = null, $attributes = array())
	{
		return $this->appendChild($this->ownerDocument->createElement($name, $value, $attributes));
	}

	public function addElementNS($namespace, $name, $value = null, $attributes = array())
	{
		return $this->appendChild($this->ownerDocument->createElementNS($namespace, $name, $value, $attributes));
	}

}
