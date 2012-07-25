<?php
/**
 * Core\View-Class
 *
 * PHP version 5.3
 *
 * @category View
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */

namespace Core;

use ArrayObject,
    Exception,
    jamwork\common\Registry;

/**
 * Core\View
 *
 * The view for the MVC pattern. This class holds the template and all variables
 * assigned to it. It also supports "template stacking". All added templates will
 * be rendered in reverse order and can be accessed inside the template with the
 * content variable.
 *
 * Also a search stack has been added for version 2. The first Core Framework always
 * required the full path to the template. With the stack its possible to define
 * places to look for the template in case it hasn't been found with the name provided.
 * 
 * @category View
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class View extends ArrayObject
{

	/**
	 * Templates
	 *
	 * @var   array
	 */
	protected $templates = array();

	/**
	 * Helpers
	 *
	 * @var   array
	 */
	protected $helpers = array();

	/**
	 * Router
	 *
	 * @var   Router
	 */
	protected $router;

	/**
	 * Holds the folders to search for the view
	 *
	 * @var string[]
	 */
	protected $searchStack = array();

	/**
	 * Holds the HTMLHelper
	 *
	 * @var HTMLHelper
	 */
	public $html;

	/**
	 * Holds title information
	 * 
	 * @var array
	 */
	protected $pageTitle = array();
	
	/**
	 * Holds page description
	 * 
	 * @var string
	 */
	protected $pageDescription = '';
	
	/**
	 * Holds keywords
	 * 
	 * @var array
	 */
	protected $pageKeywords = array();
	
	/**
	 * Constructor
	 *
	 * @param string $template Template filename
	 */
	public function __construct($template = null)
	{
		parent::__construct(array(), self::ARRAY_AS_PROPS);

		if ($template !== null)
		{
			$this->setTemplate($template);
		}

		$this->setRouter(Registry::getInstance()->router);
	}

	/**
	 * Set rendered view as string
	 * Cannot throw exceptions
	 *
	 * @return string
	 */
	public function __toString()
	{
		
		try
		{
			return $this->render();
		}
		catch(Exception $e)
		{
			return $e->getMessage();
		}
	}

	/**
	 * Returns a variable
	 *
	 * Returns NULL if variable has not been found
	 *
	 * @param string $key Key
	 * 
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		return $this->offsetExists($key) ? parent::offsetGet($key) : null;
	}

	/**
	 * Set router
	 * Required to build urls in view via the url method
	 *
	 * @param Core\Router $router Router
	 * 
	 * @return Core\Router
	 */
	public function setRouter(Router $router)
	{
		$this->router = $router;
		return $this->router;
	}

	/**
	 * Get name from current route
	 *
	 * @return Core\Route
	 */
	public function getRoute()
	{
		$route = $this->router->getCurrent() ?: 'default';
		try {
			return $this->router[$route];
		} 
		catch (\Exception $e)
		{
			throw new \ErrorException("Specified route $route not found");
		}
	}

	/**
	 * Create url
	 *
	 * @param array   $data     Parameters to set
	 * @param string  $route    Route
	 * @param boolean $reset    Reset parameters from previous match
	 * @param boolean $absolute Absolute url
	 *
	 * @return string
	 */
	public function url(array $data = array(), $route = null, $reset = null, $absolute = false)
	{
		if (!$route)
		{
			$route = $this->router->getCurrent();
		}
		return $this->router[$route]->url($data, $reset, $absolute);
	}

	/**
	 * Set title for page
	 *
	 * @param string $title Title
	 * 
	 * @return array
	 */
	public function setTitle($title)
	{
		$this->pageTitle = array($title);
		return $this->pageTitle;
	}

	/**
	 * Add part to title for page
	 *
	 * @param string $title Title
	 * 
	 * @return array
	 */
	public function addTitle($title)
	{
		$this->pageTitle[] = $title;
		return $this->pageTitle;
	}

	/**
	 * Get title
	 * Parts of title returned in reverse order and separated with $separator
	 *
	 * @param string $separator Seperator between parts of title
	 * 
	 * @return string
	 */
	public function getTitle($separator=' - ')
	{
		if (!isset($this->pageTitle))
		{
			return 'No title set';
		}
		return implode($separator, array_map('htmlspecialchars', array_reverse($this->pageTitle)));
	}

	/**
	 * Add keyword
	 * Add keyword for HTML meta tags
	 *
	 * @param string $keyword Keyword
	 * 
	 * @return array Keywords
	 */
	public function addKeyword($keyword)
	{
		$this->pageKeywords[] = $keyword;
		return $this->pageKeywords;
	}

	/**
	 * Add keywords
	 * Add keywords for HTML meta tags
	 *
	 * @param string $keyword[,...] Keywords
	 * 
	 * @return array
	 */
	public function addKeywords()
	{
		$keywords = func_get_args();
		array_walk($keywords, array($this, 'addKeyword'));
	}

	/**
	 * Get sorted keywords as string
	 * Keywords are sorted and escaped with htmlspecialchars
	 *
	 * @param string $separator Seperator
	 * 
	 * @return string Keywords
	 */
	public function getKeywords($separator=', ')
	{
		if (!isset($this->pageKeywords))
		{
			return false;
		}
		sort($this->pageKeywords);
		return implode($separator, array_map('htmlspecialchars', $this->pageKeywords));
	}

	/**
	 * Set description
	 * Set description for HTML meta tags
	 *
	 * @param string $description Description
	 * 
	 * @return string
	 */
	public function setDescription($description)
	{
		$this->pageDescription = $this->html->truncate(
			preg_replace('#\s+#', ' ', strip_tags($description)),
			140
		);
	}

	/**
	 * Get description
	 * 
	 * @return array
	 */
	public function getDescription()
	{
		if (!isset($this->pageDescription))
		{
			return null;
		}
		return htmlspecialchars($this->pageDescription);
	}

	/**
	 * Set template and remove all previous
	 *
	 * @param string $template Template filename
	 * 
	 * @return array
	 */
	public function setTemplate($template)
	{
		$this->templates = array($template);
		return $this->templates;
	}

	/**
	 * Add template to stack
	 *
	 * @param string $template Template filename
	 * 
	 * @return array
	 */
	public function addTemplate($template)
	{
		$this->templates[] = $template;
		return $this->templates;
	}

	/**
	 * Remove templates from stack
	 *
	 * @return array
	 */
	public function removeTemplates()
	{
		$this->templates = array();
		return $this->templates;
	}

	/**
	 * Get templates
	 *
	 * @return array
	 */
	public function getTemplates()
	{
		return $this->templates;
	}

	/**
	 * Render templates
	 *
	 * @param string|null $template Template filename
	 * 
	 * @return string
	 */
	public function render($template = null)
	{
		if ($template !== null)
		{
			$this->setTemplate($template);
		}

		if (count($this->templates) === 0)
		{
			return '';
		}

		foreach (array_reverse($this->templates) as $template)
		{
			try 
			{
				ob_start();
				include $template;
				$this->content = ob_get_clean();
			}
			catch(Exception $e) 
			{
				ob_end_clean();

				$this->content = '<div style="background: #f99; padding: 0.5em; margin: 0.5em;';
				$this->content .= ' border: 1px solid #f00;">'.$e->getMessage();
				$this->content .= '<br />File: '.$e->getFile().':'.$e->getLine().'</div>';
				
				throw $e;
			}
        }
		return $this->content;
	}

}
