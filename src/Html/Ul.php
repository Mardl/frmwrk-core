<?php
namespace Core\Html;

/**
 * Class Ul
 *
 * @category Core
 * @package  Core\Html
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Ul extends Element
{

	private $listItems = array();

	private $renderOutput = '{label}<ul class="{class}" style="{style}" {id}>{items}</ul>{breakafter}';

	/**
	 * Konstruktor
	 *
	 * @param null  $id
	 * @param array $css
	 * @param bool  $breakafter
	 */
	public function __construct($id = null, $css = array(), $breakafter = false)
	{
		parent::__construct($id, $css, $breakafter);

		if (file_exists(APPLICATION_PATH . '/Layout/Html/ul.html.php'))
		{
			$this->renderOutput = file_get_contents(APPLICATION_PATH . '/Layout/Html/ul.html.php');
		}
	}

	/**
	 * @param ListItem $item
	 * @return void
	 */
	public function addItem(ListItem $item)
	{
		$this->listItems[] = $item;
	}

	/**
	 * @return mixed|string
	 */
	public function __toString()
	{
		if (empty($this->listItems))
		{
			return "Liste ohne Listenpunkte.";
		}

		$output = $this->renderStandard($this->renderOutput);

		$items = '';
		foreach ($this->listItems as $item)
		{
			$items .= $item;
		}

		$output = str_replace('{items}', $items, $output);
		$output = str_replace('{label}', $this->getLabel(), $output);

		return $output;
	}
}
