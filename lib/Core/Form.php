<?php
namespace Core;

class Form{

	private $method = 'post';
	private $action = null;
	private $id = 'formular';
	private $captcha = false;
	private $attributes = array();

	protected $elements = array();
	protected $values = array();

	public function __construct($data = array())
	{
		$this->values = $data;
	}

	public function getValue($key)
	{
		if (array_key_exists($key, $this->values))
		{
			return $this->values[$key];
		}
		return null;
	}

	public function setMethod($method){
		$this->method = $method;

	}

	public function setAction($action){
		$this->action = $action;

	}

	public function setId($id){
		$this->id = $id;

	}

	public function getId(){
		if (!empty($this->id)){
			return "id='".$this->id."'";
		}
		return null;

	}

	public function addAttribute(array $attr){
		$this->attributes[] = $attr;

	}

	public function addElement(Html\Element $element){
		$this->elements[] = $element;

	}

	public function hasElements()
	{
		return (count($this->elements) > 0);
	}

	public function getElements()
	{
		return $this->elements;
	}

	public function addElements(array $elements){
		foreach ($elements as $element){
			$this->addElement($element);
		}

	}

	public function __toString(){
		$elements = '';
		foreach ($this->elements as $element){
			$elements .= $element;
		}

		$attributes = '';
		foreach ($this->attributes as $attr)
		{
			$attributes = $attr[0].'="'.$attr[1].'" ';
		}

		$output = file_get_contents(APPLICATION_PATH.'/Layout/Form/form.html.php');
		$output = str_replace('{method}', $this->method, $output);
		$output = str_replace('{action}', $this->action, $output);
		$output = str_replace('{id}', $this->getId(), $output);
		$output = str_replace('{attributes}', $attributes, $output);
		$output = str_replace('{elements}', $elements, $output);

		return $output;

	}

	public function updateElement($elementId, $value, $updateType, $container = null)
	{
		if (is_null($container))
		{
			$container = $this;
		}

		foreach ($container->getElements() as $el){
			if ($el->getPlainId() == $elementId)
			{
				$el->$updateType($value);
				return;
			}

			if ($el->hasElements()){
				$this->updateElement($elementId, $value, $updateType, $el);
			}
		}
	}

	public function findElement($elementId, $container = null)
	{
		$target = null;

		if (is_null($container))
		{
			$container = $this;
		}

		foreach ($container->getElements() as $el){
			if ($el->getPlainId() == $elementId)
			{
				return $el;
			}

			if ($el->hasElements()){
				$target = $this->findElement($elementId, $el);
			}
		}

		return $target;

	}

}
?>