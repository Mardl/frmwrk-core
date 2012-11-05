<?php
/**
 * Core\Router-Class
 *
 * PHP version 5.3
 *
 * @category Routing
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */

namespace Core;

use ArrayObject;

/**
 * Router
 *
 * @category Routing
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class Router extends ArrayObject
{

	/**
	 * Current route name
	 *
	 * @var string
	 */
	protected $current;

	/**
     * Current route
     *
	 * @var Core\Route
	 */
	protected $currentRoute;

    /**
     * Parameters from last match
     *
     * @var array
     */
	protected $params = array();

	/**
	 * Get route by name
	 *
	 * @param string $route Name of route
	 *
	 * @return Core\Route
	 */
	public function offsetGet($route)
	{
		if (!isset($this[$route]))
		{
			return parent::offsetGet('default');
		}
		return parent::offsetGet($route);
	}

	/**
	 * Liefert eine bestimmte Route anhand ihres Namens
	 *
	 * @param string $routeName Routenname
	 *
	 * @return Core\Route
	 */
    public function getRoute($routeName)
    {
        return $this[$routeName];
    }

	/**
	 * Add route
	 *
	 * @param string $key      Name of Route
	 * @param string $path     Path with placeholders
	 * @param array  $defaults Default values for route
	 *
	 * @return Core\Route
	 */
	public function addRoute($key, $path, array $defaults = array())
	{
		$route = new Route($path, $defaults);
		$route->setRouter($this);
		$this[$key] = $route;
		return $this[$key];
	}

    /**
     * Add routes
     *
     * @param array $routes Routeninformationen
     *
     * @return void
     */
	public function addRoutes(array $routes)
	{
		foreach ($routes as $route)
		{
			$this->addRoute($route['key'], $route['path'], $route['defaults']);
		}
	}

	/**
	 * Search route matching $url
	 *
	 * @param string $url URL
	 *
	 * @return Core\Route|boolean
	 */
	public function searchRoute($url)
	{
		foreach ($this as $key => $route)
		{
			if ($route->match($url))
			{
				$this->current = $key;
				$this->currentRoute = $route;
				return $route;
			}
		}
		return false;

	}

	/**
	 * Find route matching $url
	 *
	 * @param string $url URL
	 *
	 * @return Core\Route|boolean
	 */
	public function findRoute($url)
	{
		foreach ($this as $key => $route)
		{
			$routeData = $route->matchUrl($url);
			if ($routeData)
			{
				return $routeData;
			}
		}
		return false;

	}

	/**
	 * Get current route name
	 *
	 * @return string
	 */
	public function getCurrent()
	{
		return $this->current;
	}

    /**
     * Get current route
     *
     * @return Core\Route
     */
	public function getCurrentRoute()
	{
		return $this->currentRoute;
	}

	/**
	 * Speichert einen Parameter ab und gibt true zurück wenn dies erfolgreich war
	 *
	 * @param string $key   Parametername
	 * @param mixed  $value Wert
	 *
	 * @return boolean
	 */
	public function setParam($key, $value)
	{
		return $this->params[$key] = $value;
	}


	/**
     * Get params from route
     *
     * @return array
     */
	public function getParams()
	{
		return $this->params;
	}

    /**
     * Get param from route by name
     *
     * @param string $key Parametername
     *
     * @return string
     */
	public function getParam($key)
	{
		if (array_key_exists($key, $this->params))
		{
			return $this->params[$key];
		}
		return null;

	}

    /**
     * Set params
     *
     * @param array $params Array mit Parametern
     *
     * @return void
     */
	public function setParams($params)
	{
		$this->params = $params;
	}

}
