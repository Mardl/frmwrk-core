<?php

namespace Core;

use ArrayObject;
use jamwork\common\Registry;

/**
 * Class Router
 *
 * @category Core
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
	 * @var \Core\Route
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
	 * @return \Core\Route
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
	 * @return \Core\Route
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
	 * @return \Core\Route
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
	 * @param string $url
	 * @return string
	 */
	public function cleanBaseUrl($url)
	{
		$link = '';
		try
		{
			$link = Registry::getInstance()->conf->BASE_URL;
		} catch (\Exception $e)
		{
		}
		if (empty($link))
		{
			$link = '/';
		}
		$baseLength = strlen($link)-1;
		if ($baseLength > 0)
		{
			if (substr($url, 0, $baseLength+1) == $link)
			{
				$url = substr($url, $baseLength);
			}
			else
			{
				// Base Url ohne leading Slash
				$relativBaseUrl = substr($link, 1);
				if (substr($url, 0, $baseLength) == $relativBaseUrl)
				{
					$url = substr($url, $baseLength);
				}
			}
		}
		return $url;
	}

	/**
	 * Search route matching $urlIncomming
	 *
	 * @param string $urlIncomming URL
	 *
	 * @return \Core\Route|boolean
	 */
	public function searchRoute($urlIncomming)
	{
		$url = $this->cleanBaseUrl($urlIncomming);

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
	 * @param string $urlIncomming URL
	 * @param bool   $instance     Instanz
	 *
	 * @return bool|\Core\Route
	 */
	public function findRoute($urlIncomming, $instance = false)
	{
		$url = $this->cleanBaseUrl($urlIncomming);
		/** @var $route \Core\Route */
		foreach ($this as $key => $route)
		{
			$routeData = $route->matchUrl($url);
			if ($routeData)
			{
				if (!$instance)
				{
					return $routeData;
				}
				else
				{
					return $route;
				}
			}
		}

		return false;
	}

	/**
	 * @param mixed $data
	 * @return mixed
	 */
	public function getRouteByArray($data)
	{
		$matching = array();
		foreach ($this as $routeName => $value)
		{
			if ($value instanceof \Core\Route && $routeName != 'default')
			{
				$temp = $value->getDefaults();
				$matching[$routeName] = 0;
				foreach ($data as $key => $val)
				{
					if (array_key_exists($key, $temp))
					{
						if ($key == 'module' && $val == $temp['module'])
						{
							$matching[$routeName] += 3;
						}
						else
						{
							if ($key == 'controller' && $val == $temp['controller'])
							{
								$matching[$routeName] += 2;
							}
							else
							{
								if ($key == 'action' && $val == $temp['action'])
								{
									$matching[$routeName] += 1;
								}
								else
								{
									if ($key == 'module' && $val != $temp['module'])
									{
										$matching[$routeName] -= 3;
									}
									else
									{
										if ($key == 'controller' && $val != $temp['controller'])
										{
											$matching[$routeName] -= 2;
										}
										else
										{
											if ($key == 'action' && $val != $temp['action'])
											{
												$matching[$routeName] -= 1;
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		$max = 0;
		$winnerRoute = null;

		foreach ($matching as $route => $count)
		{
			if ($count > $max)
			{
				$max = $count;
				$winnerRoute = $route;
			}
		}

		if (is_null($winnerRoute))
		{
			$winnerRoute = 'default';
		}

		return $this[$winnerRoute];
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
	 * @return \Core\Route
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
