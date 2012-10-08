<?php
namespace Core\Html;

class Div extends Element{


	public function __construct($css = array(), $breakafter=false){
		$this->breakafter = $breakafter;
		$this->addCssClasses($css);
	}

	public function __toString(){
		$elements = '';
		foreach ($this->elements as $element){
			$elements .= $element;
		}

		$output = file_get_contents(APPLICATION_PATH.'/Layout/Form/div.html.php');
		$output = str_replace('{class}', $this->getCssClasses(), $output);
		$output = str_replace('{style}', $this->getInlineCss(), $output);
		$output = str_replace('{id}', $this->getId(), $output);
		$output = str_replace('{elements}',$elements, $output);
		$output = str_replace('{attr}', $this->renderAttributes(), $output);

		if ($this->breakafter)
		{
			$output = str_replace('{breakafter}', '<br class="clear"/>', $output);
		}
		else
		{
			$output = str_replace('{breakafter}', null, $output);
		}


		return $output;

	}

}
?>