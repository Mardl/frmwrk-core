<?php

namespace Core;

use ArrayObject, Exception, jamwork\common\Registry;

/**
 * Class View
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
 * @category Core
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

	protected $placeholder = array();

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

		$this->html = new \Core\HTMLHelper($this);
		$this->html->addCssAsset('default');

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
		} catch (Exception $e)
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
	 * Required to build URLs in view via the URL method
	 *
	 * @param Router $router Router
	 * @return Router
	 */
	public function setRouter(Router $router)
	{
		$this->router = $router;

		return $this->router;
	}

	/**
	 * Get name from current route
	 *
	 * @return mixed
	 * @throws \ErrorException
	 */
	public function getRoute()
	{
		$route = $this->router->getCurrent() ? : 'default';
		try
		{
			return $this->router[$route];
		} catch (\Exception $e)
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
	public function getTitle($separator = ' - ')
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
	 * @internal param string $keyword [,...] Keywords
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
	public function getKeywords($separator = ', ')
	{
		if (empty($this->pageKeywords))
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
		$this->pageDescription = $this->html->truncate(preg_replace('#\s+#', ' ', strip_tags($description)), 140);
	}

	/**
	 * Get description
	 *
	 * @return array
	 */
	public function getDescription()
	{
		if (empty($this->pageDescription))
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
	 * @param string $key
	 * @param mixed  $value
	 * @param bool   $prerender
	 * @return void
	 */
	public function addPlaceholder($key, $value, $prerender = false)
	{
		if ($prerender)
		{
			$this->placeholder[$key] = $value . '';
		}
		else
		{
			$this->placeholder[$key] = $value;
		}
	}

	/**
	 * Fügt mehrere Inhalte zu einem Placeholder hinzu
	 *
	 * @param string $key   Placeholdername
	 * @param mixed  $value Content
	 * @return void
	 */
	public function addMultiPlaceholder($key, $value)
	{
		if (!array_key_exists($key, $this->placeholder))
		{
			$this->placeholder[$key] = array();
		}

		if (!is_array($this->placeholder[$key]))
		{
			$this->placeholder[$key] = array();
		}

		$this->placeholder[$key][] = $value;
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function getPlaceholder($key)
	{
		return $this->placeholder[$key];
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
	 * @param null $template Template filename
	 *
	 * @return string
	 *
	 * @throws \Exception
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


				foreach ($this->placeholder as $key => $value)
				{
					$c = $value;

					if (is_array($value))
					{
						$c = '';
						foreach ($value as $val)
						{
							if (!empty($val))
							{
								$c .= $val;
							}

						}

					}

					if (strpos($this->content, '{' . $key . '}') !== false)
					{
						$this->content = str_replace('{' . $key . '}', $c, $this->content);
					}

				}
			} catch (Exception $e)
			{
				ob_end_clean();

				$this->content = '<div style="background: #f99; padding: 0.5em; margin: 0.5em;';
				$this->content .= ' border: 1px solid #f00;">' . $e->getMessage();
				$this->content .= '<br />File: ' . $e->getFile() . ':' . $e->getLine() . '</div>';

				throw $e;
			}
		}

		return $this->content;
	}

	/**
	 * Formatiert das Datum in d.m.Y (z.B. 01.01.1970)
	 *
	 * @param string $date
	 * @return string
	 */
	public function formatDate($date)
	{
		return $date->format('d.m.Y');
	}

	/**
	 * Formatiert den Wert in ein Währungsformat um
	 *
	 * @param string $value      Input
	 * @param string $currency   Währungseinheit (z.B. $ oder €)
	 * @return string
	 */
	public function convertToCurrency($value, $currency = '€')
	{
		$number = number_format($value, 2, ',', '.');

		return $number . " $currency";
	}

	/**
	 * @param string $value
	 * @return string
	 */
	public function formatNumberStyle($value)
	{
		if (!empty($value))
		{
			return $this->convertToCurrency($value);
		}

		return '-';
	}
}
