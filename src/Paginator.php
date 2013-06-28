<?php

namespace Core;

/**
 * Class Paginator
 * Paginator
 * Einfache Umsetzung einer Blätterfunktion
 *
 * @category Core
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class Paginator
{

	/**
	 * Aktuelle Seite
	 *
	 * @var int
	 */
	private $_page;

	/**
	 * Gesamtanzahl der Items
	 *
	 * @var int
	 */
	private $_absoluteCount;

	/**
	 * Anzahl der Items, die angezeigt werden sollen
	 *
	 * @var int
	 */
	private $_itemsPerPage;

	private $_class = 'pgright';

	private $_pointCount = 5;

	/**
	 * Konstruktur
	 *
	 * @param int $absoluteCount  Gesamtanzahl
	 * @param int $page           Aktuell geöffnete Seite
	 * @param int $itemsPerPage   Anzahl der Items pro Seite
	 */
	public function __construct($absoluteCount, $page = 0, $itemsPerPage = 25)
	{
		$this->_page = $page;
		$this->_absoluteCount = $absoluteCount;
		$this->_itemsPerPage = $itemsPerPage;
	}

	/**
	 * Liefert die Anzahl der Items pro Seite
	 *
	 * @return int
	 */
	public function getLimit()
	{
		return $this->_itemsPerPage;
	}

	/**
	 * Liefert den Offset für die DB-Abfrage
	 *
	 * @return int
	 */
	public function getOffset()
	{
		return ($this->_page * $this->_itemsPerPage);
	}

	/**
	 * @param string $class
	 * @return void
	 */
	public function setClass($class)
	{
		$this->_class = $class;
	}

	/**
	 * Erstellt mittels HTML-Snippet die Anzeige des Paginators
	 *
	 * @return string
	 */
	public function __toString()
	{
		$steps = ($this->_absoluteCount / $this->_itemsPerPage);

		if ($steps > 1)
		{
			$view = new View(APPLICATION_PATH . '/Layout/Helpers/paginator.html.php');
			$view->steps = ceil($steps);
			$view->last = $view->steps - 1;
			$view->current = $this->_page;
			$view->class = $this->_class;


			if ($view->current < ($steps - 1))
			{
				$view->next = $view->current + 1;
			}
			else
			{
				$view->next = $view->steps - 1;
			}

			if ($this->_page > 0)
			{
				$view->prev = $this->_page - 1;
			}
			else
			{
				$view->prev = 0;
			}

			$view->start = 0;
			$view->end = $view->steps;

			if ($view->steps > $this->_pointCount)
			{
				$view->end = $view->current + 3;
				$view->start = $view->current - 2;

				if ($view->start < 0)
				{
					$view->start = 0;
				}

				if ($view->end > $view->steps)
				{
					$view->end = $view->steps;
				}
			}

			return $view->render();
		}

		return '';
	}
}
