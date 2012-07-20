<?php
/**
 * Core\Paginator-Class
 *
 * PHP version 5.3
 *
 * @category Helper
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace Core;

/**
 * Paginator
 * Einfache Umsetzung einer Blätterfunktion
 *
 * @category Helper
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class Paginator
{
	
	/**
	 * Aktuelle Seite
	 * 
	 * @var integer
	 */
	private $_page;
	
	/**
	 * Gesamtanzahl der Items
	 * 
	 * @var integer
	 */
	private $_absoluteCount;
	
	/**
	 * Anzahl der Items, die angezeigt werden sollen
	 * 
	 * @var integer
	 */
	private $_itemsPerPage;
	
	/**
	 * Konstruktur
	 * 
	 * @param integer $absoluteCount Gesamtanzahl
	 * @param integer $page          Aktuell geöffnete Seite
	 * @param integer $itemsPerPage  Anzahl der Items pro Seite
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
	 * @return integer
	 */
	public function getLimit()
	{
		return $this->_itemsPerPage;
	}
	
	/**
	 * Liefert den Offset für die DB-Abfrage
	 * 
	 * @return integer
	 */
	function getOffset()
	{
		return ($this->_page * $this->_itemsPerPage);
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
			$view = new View(APPLICATION_PATH.'/Layout/Helpers/paginator.html.php');
			$view->steps = ceil($steps);
			$view->last = $view->steps - 1;
			$view->current = $this->_page;
			
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
			
			
			return $view->render();
		}
		
		return '';
	}
	
}

?>