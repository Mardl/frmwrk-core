<?php
/**
 * Core\Route-Class
 *
 * PHP version 5.3
 *
 * @category Routing
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */

namespace Core;

use Core\Request;

/**
 * Route
 * 
 * @category Routing
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class Route
{

    /**
     * Router
     *
     * @var Core\Router;
     */
	protected $router;

	/**
	 * Pattern
	 *
	 * @var string
	 */
	protected $pattern;

	/**
	 * Default values defined in route
	 *
	 * @var array
	 */
	protected $defaults = array();

	/**
	 * Constructor
	 *
	 * @param string $pattern  Pattern
	 * @param array  $defaults Default values
	 * 
	 * @return void
	 */
	public function __construct($pattern, array $defaults = array())
	{
		$this->pattern = $pattern;
		$this->defaults = $defaults;
	}

    /**
     * Get router
     *
     * @return Core\Router
     */
	public function getRouter() 
	{
		return $this->router;
	}

    /**
     * Set router
     *
     * @param Core\Router $router Router Object
     * 
     * @return void
     */
	public function setRouter(Router $router)
	{
		$this->router = $router;
	}

	/**
	 * Get param value from route
	 *
	 * @param string $key Key
	 * 
	 * @return string
	 */
	public function get($key)
	{
		return $this->getRouter()->getParam($key);
	}

	/**
	 * Get all values from route
	 *
	 * @return array
	 */
	public function getParams()
	{
		return $this->getRouter()->getParams();
	}

	/**
	 * Match $url to route
	 *
	 * @param string $url Url
	 * 
	 * @return boolean
	 */
	public function match($url)
	{
		$url = parse_url($url);
		$result = $this->defaults;

		// Get file extension
		$temp = pathinfo($url['path']);
		
		/*
		if (!array_key_exists('dirname', $temp))
		{
			$temp['dirname'] = '';
		}
		*/
		
		$url['path'] = $temp['dirname'].'/'.$temp['filename'];
		
		if (isset($temp['extension']) && $temp['extension'])
		{
			$result['format'] = $temp['extension'];
		}
		else
		{
			$result['format'] = 'html';
		}

		$parts = explode('/', $this->pattern);
		$parts = array_values(array_filter($parts));
		
		$urlparts = explode('/', $url['path']);
		$urlparts = array_values(array_filter($urlparts));
	
		foreach ($parts as $key => $part)
		{
			/*if (!$part)
			{
				continue;
			}*/
			
			// Constant part not exists
			if (substr($part, 0, 1) != ':' && !isset($urlparts[$key]))
			{
				return false;
			}
			
			// Constant part wrong
			if (substr($part, 0, 1) != ':' && $urlparts[$key] != $part)
			{
				return false;
			}

            // Save dynamic value to $result
			if (isset($urlparts[$key]) && $urlparts[$key])
			{
				$result[substr($part, 1)] = $urlparts[$key];
			}
		}

		$this->getRouter()->setParams($result);
		return $result;

	}

	/**
	 * Create url from route
	 *
	 * @param array        $params   Parameters
	 * @param boolean|null $reset    Reset values from last match
	 * @param boolean      $absolute Absolute Url with hostname
	 *
	 * @return string
	 */
	public function url(array $params = array(), $reset = null, $absolute = false)
	{
		$defaults = $this->defaults;
		if ($reset === null && $this == $this->getRouter()->getCurrentRoute() || $reset === false)
		{
			$defaults = ($this->getRouter()->getParams() + $defaults);
		}

		$parts = preg_split('/[\/\.]/', $this->pattern, 32, PREG_SPLIT_NO_EMPTY);
		
		foreach ($parts as $key => $part)
		{
			// Add constant parts
			if (substr($part, 0, 1) != ':')
			{
				$result[] = $part;
				continue;
			}

			if (substr($part, 0, 1) == ':' && isset($params[substr($part, 1)]))
			{
				// Replace values from parameters
				$result[substr($part, 1)] = String::slug($params[substr($part, 1)]);
				unset($params[substr($part, 1)]);
			} 
			else if (isset($defaults[substr($part, 1)]))
			{
				// Replace default values
				$result[substr($part, 1)] = $defaults[substr($part, 1)];
			} 
			else 
			{
				throw new \ErrorException(
					'Key '.substr($part, 1).' is not defined in route '.$this->pattern
				);
			}
		}
		
		// Remove values already defined in defaults
		foreach ($params as $key => $value) 
		{
			if (!isset($this->defaults[$key]))
			{
				continue;
			}
			
			if ($this->defaults[$key] != $value)
			{
				continue;
			}
			
			if ($key == 'format')
			{
				continue;
			}
			
			unset($params[$key]);
		}

		// Remove backslash
		foreach (array_reverse($result) as $key => $value)
		{
			if ($value != 'index')
			{
				break;
			}
			unset($result[$key]);
		}

		if ($absolute or isset($params['hostname']))
		{
			if (isset($params['hostname']))
			{
				$url = 'http://'.$params['hostname'].'/';
			}
			else if (isset($defaults['hostname']))
			{
				$url = 'http://'.$defaults['hostname'].'/';
			} 
			else if (isset($_SERVER['HTTP_HOST']))
			{
				$url = 'http://'.$_SERVER['HTTP_HOST'].'/';
			} 
			else if (defined('DOMAIN'))
			{
				$url = 'http://'.DOMAIN.'/';
			} 
			else 
			{
				$url = 'http://'.Request::getHost().'/';
			}
		} 
		else 
		{
			$url = '/';
		}
		unset($params['hostname']);

		$url .= implode('/', $result);
		if (strlen($url) > 1 && substr($url, -1) != '/')
		{
			if (isset($params['format']))
			{
				$url .= '.'.$params['format'];
				unset($params['format']);
			} 
			else if (isset($defaults['format']))
			{
				$url .= '.'.$defaults['format'];
			}
		}
		else 
		{
			if (isset($params['format']))
			{
				$url .= 'index.'.$params['format'];
				unset($params['format']);
			}
		}
		

		if (isset($params['anchor']))
		{
			$anchor = $params['anchor'];
			unset($params['anchor']);
		}

		if ($params)
		{
			ksort($params);
			$query = http_build_query($params);
			if ($query)
			{
				$url .= '?'.http_build_query($params);
			}
		}

		if (isset($anchor))
		{
			$url .= '#'.$anchor;
		}

		return $url;

	}

}
