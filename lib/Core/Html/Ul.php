<?php
namespace Core\Html;

class Ul extends Element{

	private $listItems = array();

	private $renderOutput = '{label}<ul class="{class}" style="{style}" {id}>{items}</ul>{breakafter}';

	public function __construct($id = null, $css = array(), $breakafter = false){
		parent::__construct($id, $css, $breakafter);

		if (file_exists(APPLICATION_PATH.'/Layout/Form/ul.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH.'/Layout/Form/ul.html.php');
		}
	}


	public function addItem(ListItem $item){
		$this->listItems[] = $item;

	}

	public function __toString(){

		if (empty($this->listItems)){
			return "Liste ohne Listenpunkte.";
		}

		$output = $this->renderStandard($this->renderOutput);

		$items = '';
		foreach ($this->listItems as $item){
			$items .= $item;
		}

		$output = str_replace('{items}', $items, $output);
		$output = str_replace('{label}', $this->getLabel(), $output);


		return $output;
	}

}