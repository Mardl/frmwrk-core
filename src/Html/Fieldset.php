<?php
namespace Core\Html;

class Fieldset extends Element{

	private $legend = null;
	private $renderOutput = '<fieldset class="{class}" style="{style}" {id}>{legend}{elements}</fieldset>{breakafter}';

	public function __construct($legend = null, $id='', $css = array(), $breakafter = false){
		parent::__construct($id, $css, $breakafter);
		$this->setLegend($legend);
		if (file_exists(APPLICATION_PATH.'/Layout/Html/fieldset.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH.'/Layout/Html/fieldset.html.php');
		}
	}

	public function setLegend($legend){
		$this->legend = $legend;

	}

	public function __toString()
	{
		$output = $this->renderStandard($this->renderOutput);

		if (!is_null($this->legend))
		{
			$output = str_replace('{legend}', "<legend>".$this->legend."</legend>", $output);
		}
		else
		{
			$output = str_replace('{legend}', $this->legend, $output);
		}

		return $output;

	}

}
?>