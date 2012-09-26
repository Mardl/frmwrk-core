<?php

namespace Core\DOM;

class Document extends \DOMDocument {

	public function __construct($version = '1.0', $encoding = 'UTF-8') {
		parent::__construct($version, $encoding);
		$this->formatOutput = true;
		$this->registerNodeClass('DOMElement', 'Core\\DOM\\Element');
	}

	public function __toString() {
		return $this->saveXML();
	}

	public function loadHTML($html) {
		libxml_use_internal_errors(true);
		$result = parent::loadHTML($html);
		libxml_use_internal_errors(false);
		return $result;
	}

	public function loadHTMLFile($filename) {
		libxml_use_internal_errors(true);
		$result = parent::loadHTMLFile($filename);
		libxml_use_internal_errors(false);
		return $result;
	}

	public function createXPath() {
		return new \DOMXPath($this);
	}

	public function createElement($name, $value = NULL, $attributes = array()) {
		if($value) {
			$element = parent::createElement($name, $value);
		} else {
			$element = parent::createElement($name);
		}
		$element->setAttributes($attributes);
		return $element;
	}

	public function addElement($name, $value = NULL, $attributes = array()) {
		return $this->appendChild($this->createElement($name, $value, $attributes));
	}

	public function createElementNS($namespace, $name, $value = NULL, $attributes = array()) {
		if($value) {
			$element = parent::createElementNS($namespace, $name, $value);
		} else {
			$element = parent::createElementNS($namespace, $name);
		}
		$element->setAttributes($attributes);
		return $element;
	}

	public function addElementNS($namespace, $name, $value = NULL, $attributes = array()) {
		return $this->appendChild($this->createElementNS($namespace, $name, $value, $attributes));
	}

}
