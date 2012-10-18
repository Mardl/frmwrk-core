<?php
namespace Core\Html;

class Input extends Element{

	protected $type = 'text';
	protected $placeholder = '';
	private $renderOutput = '{label}<input type="{type}" class="{class}" style="{style}" {id} name="{name}" value="{value}" {placeholder} {readonly} {attr}/>{breakafter}';

	public function __construct($id, $default, $css = array(), $breakafter = false){
		parent::__construct($id, $css, $breakafter);

		if (file_exists(APPLICATION_PATH.'/Layout/Html/input.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH.'/Layout/Html/input.html.php');
		}

		$this->setValue($default);

	}

	public function setType($type){
		$this->type = $type;

	}

	public function setPlaceholder($placeholder){
		$this->placeholder = $placeholder;

	}

	public function __toString(){



		$output = $this->renderStandard($this->renderOutput);

		$output = str_replace('{placeholder}', empty($this->placeholder) ? '' : 'placeholder="'.$this->placeholder.'"', $output);
		$output = str_replace('{type}', $this->type, $output);
		$output = str_replace('{name}', $this->getName(), $output);
		$output = str_replace('{label}', $this->getLabel(), $output);
		$output = str_replace('{value}', htmlspecialchars($this->getValue()), $output);
		$output = str_replace('{readonly}', $this->getReadonly() ? 'readonly' : '', $output);

		return $output;
	}

	public function setValue($value){
		$this->value = $value;

	}

	public function getValue(){
		return $this->value;

	}

}