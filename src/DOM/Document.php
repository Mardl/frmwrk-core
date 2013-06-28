<?php

namespace Core\DOM;

/**
 * Class Document
 *
 * @category Core
 * @package  Core\DOM
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Document extends \DOMDocument
{

	/**
	 * Konstruktor
	 *
	 * @param string $version
	 * @param string $encoding
	 */
	public function __construct($version = '1.0', $encoding = 'UTF-8')
	{
		parent::__construct($version, $encoding);
		$this->formatOutput = true;
		$this->registerNodeClass('DOMElement', 'Core\\DOM\\Element');
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->saveXML();
	}

	/**
	 * @param string $html
	 * @return bool
	 */
	public function loadHTML($html)
	{
		libxml_use_internal_errors(true);
		$result = parent::loadHTML($html);
		libxml_use_internal_errors(false);

		return $result;
	}

	/**
	 * @param string $filename
	 * @return bool
	 */
	public function loadHTMLFile($filename)
	{
		libxml_use_internal_errors(true);
		$result = parent::loadHTMLFile($filename);
		libxml_use_internal_errors(false);

		return $result;
	}

	/**
	 * @return \DOMXPath
	 */
	public function createXPath()
	{
		return new \DOMXPath($this);
	}

	/**
	 * @param string $name
	 * @param null   $value
	 * @param array  $attributes
	 * @return \DOMElement
	 */
	public function createElement($name, $value = null, $attributes = array())
	{
		if ($value)
		{
			$element = parent::createElement($name, $value);
		}
		else
		{
			$element = parent::createElement($name);
		}
		$element->setAttributes($attributes);

		return $element;
	}

	/**
	 * @param string $name
	 * @param null   $value
	 * @param array  $attributes
	 * @return \DOMNode
	 */
	public function addElement($name, $value = null, $attributes = array())
	{
		return $this->appendChild($this->createElement($name, $value, $attributes));
	}

	/**
	 * @param string $namespace
	 * @param string $name
	 * @param null   $value
	 * @param array  $attributes
	 * @return \DOMElement
	 */
	public function createElementNS($namespace, $name, $value = null, $attributes = array())
	{
		if ($value)
		{
			$element = parent::createElementNS($namespace, $name, $value);
		}
		else
		{
			$element = parent::createElementNS($namespace, $name);
		}
		$element->setAttributes($attributes);

		return $element;
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
		return $this->appendChild($this->createElementNS($namespace, $name, $value, $attributes));
	}
}
