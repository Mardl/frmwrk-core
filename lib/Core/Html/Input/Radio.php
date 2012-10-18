<?php
namespace Core\Html\Input;

use Core\Html\Input;

class Radio extends Input{

	private $options = array();
	private $renderOutput = '<input type="radio" class="{class}" style="{style}" {id} name="{name}" value="{value}" {attr} {checked}/> {title}';

	public function __construct($id, $css = array(), $breakafter = false){
		parent::__construct($id, $css, $breakafter);

		if (file_exists(APPLICATION_PATH.'/Layout/Html/radio.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH.'/Layout/Html/radio.html.php');
		}
	}

	public function addOption($value, $tag, $selected = false){
		$this->options[] = array($value,$tag,$selected);

	}

	public function __toString(){
		$output = '';

		$count=0;
		foreach ($this->options as $option)
		{
			$count++;
			$opt = $this->renderOutput;
			$opt = str_replace('{id}', $this->getId($count), $opt);
			$opt = str_replace('{name}', $this->getName(), $opt);
			$opt = str_replace('{label}', $this->getLabel(), $opt);
			$opt = str_replace('{value}', htmlspecialchars($option[0]), $opt);
			$opt = str_replace('{title}', htmlspecialchars($option[1]), $opt);

			$opt = $this->renderStandard($opt);

			$opt = $option[2] ? str_replace('{checked}', 'checked="checked"', $opt) : str_replace('{checked}', '', $opt);

			$output .= $opt;
		}

		return $output;
	}


}