<?php

namespace Core\Html;

/**
 * Class Headline
 *
 * @category Core
 * @package  Core\Html
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Headline extends Element
{

	protected $index = 1;
	private $renderOutput = '<h{index} {id} class="{class}" style="{style}" {attr}>{elements}</h{index}>';

	/**
	 * Konstruktor
	 *
	 * @param int    $index
	 * @param string $id
	 * @param array  $css
	 * @param bool   $breakafter
	 */
	public function __construct($index = null, $id = '', $css = array(), $breakafter = false)
	{
		parent::__construct($id, $css, $breakafter);

		if (file_exists(APPLICATION_PATH . '/Layout/Html/headline.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH . '/Layout/Html/headline.html.php');
		}

		if (!is_null($index))
		{
			$this->index = $index;
		}
	}

	/**
	 * @param int $index
	 * @return void
	 */
	public function setIndex($index)
	{
		$this->index = $index;
	}

	/**
	 * @return int|null
	 */
	public function getIndex()
	{
		return $this->index;
	}

	/**
	 * @return mixed
	 */
	public function __toString()
	{
		$output = $this->renderStandard($this->renderOutput);
		$output = str_replace('{index}', $this->getIndex(), $output);

		return $output;
	}
}
