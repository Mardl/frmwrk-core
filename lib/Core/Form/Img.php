<?php
namespace Core\Form;

class Img extends Element
{
	private $path;
	private $alt = null;
	
	public function __construct($breakafter=false)
	{
		$this->breakafter = $breakafter;
	}
	
	public function setPath($path)
	{
		$this->path = $path;
	}
	
	public function setAlt($alt)
	{
		$this->alt = $alt;
	}
		
	public function __toString()
	{
		$output = file_get_contents(APPLICATION_PATH.'/Layout/Form/image.html.php');
		
		if ($this->hasCssClasses())
		{
			$output = str_replace('{class}', 'class="'.$this->getCssClasses().'"', $output);
		}
		else
		{
			$output = str_replace('{class}', '', $output);
		}
		
		$output = str_replace('{src}', $this->path, $output);
		$output = str_replace('{style}', 'style="'.$this->getInlineCss().'"', $output);
		$output = str_replace('{id}', $this->getId(), $output);

		if (!empty($this->alt))
		{
			$output = str_replace('{alt}', $this->alt, $output);
		}
		else
		{
			$output = str_replace('{alt}', $this->path, $output);
		}
		
		$output = str_replace('{attr}', $this->renderAttributes(), $output);
		
		if ($this->breakafter)
		{
			$output = str_replace('{breakafter}', '<br/>', $output);
		}
		else
		{
			$output = str_replace('{breakafter}', null, $output);
		}
		
		return $output;
	}
}