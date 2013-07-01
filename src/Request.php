<?php
/**
 * Core\Request-Class
 *
 * PHP version 5.3
 *
 * @category Routing
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */

namespace Core;

use jamwork\common\HttpRequest;

/**
 * RequestObject and extends jamwork\common\HttpRequest
 *
 * @category Core
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class Request extends HttpRequest
{

	protected static $instance;

	/**
	 * Beinhaltet die Parameter der Route
	 * modul / prefix / controller / action / format
	 * @var array
	 */
	private $routeParams = array();

	/**
	 * Konstruktor
	 *
	 * @param array $get    Array $_GET
	 * @param array $post   Array $_POST
	 * @param array $server Array $_SERVER
	 * @param array $cookie Array $_COOKIE
	 */
	public function __construct(array $get, array $post, array $server, array $cookie)
	{
		parent::__construct($get, $post, $server, $cookie);
	}

	/**
	 * @return Request
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new Request($_GET, $_POST, $_SERVER, $_COOKIE);
		}

		return self::$instance;
	}

	/**
	 * Is ajax request?
	 * Tested with Firefox 3, Opera 9, Internet Explorer 7.
	 *
	 * @return boolean
	 */
	public function isAjax()
	{
		if ($this->getServer('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest')
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Is HTTPS (with SSL)?
	 *
	 * @return boolean
	 */
	public function isHTTPS()
	{
		if ($this->getServer('HTTPS', false))
		{
			return true;
		}
		// Nginx
		if ($this->getServer('HTTP_X_CLIENT_VERIFY') == 'SUCCESS')
		{
			return true;
		}

		return false;
	}

	/**
	 * Is user agent a mobile device?
	 *
	 * @return boolean
	 */
	public static function isMobile()
	{
		$aUserAgents = array(
			'240x320',
			'benq',
			'blackberry',
			'iphone',
			'ipod',
			'mda',
			'midp',
			'mot-',
			'netfront',
			'nokia',
			'opera mini',
			'opera mobi',
			'panasonic',
			'philips',
			'pocket pc',
			'portalmmm',
			'sagem',
			'samsung',
			'sda',
			'sgh-',
			'sharp',
			'sie-',
			'sonyericsson',
			'symbian',
			'vodafone',
			'windows ce',
			'windows mobile',
			'xda'
		);

		foreach ($aUserAgents as $cUserAgent)
		{
			if (stripos($_SERVER['HTTP_USER_AGENT'], $cUserAgent) !== false)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Get HTTP method
	 *
	 * @return string|null
	 */
	public function getMethod()
	{
		return $this->getServer('REQUEST_METHOD', null);
	}

	/**
	 * Is HTTP method a GET request?
	 *
	 * @return boolean
	 */
	public function isGet()
	{
		return $this->getServer('REQUEST_METHOD') == 'GET';
	}

	/**
	 * Is HTTP method a POST request?
	 *
	 * @return boolean
	 */
	public function isPost()
	{
		return $this->getServer('REQUEST_METHOD') == 'POST';
	}

	/**
	 * Get hostname
	 *
	 * @return string
	 */
	public function getHost()
	{
		$host = $this->getServer('HTTP_HOST');
		if (!empty($host))
		{
			return $host;
		}

		if (function_exists('gethostname'))
		{
			$hostname = gethostname();
			if ($hostname)
			{
				return $hostname;
			}
		}

		$hostname = php_uname('n');
		if ($hostname)
		{
			return $hostname;
		}

		if (defined('DEFAULT_HTTP_HOST'))
		{
			return DEFAULT_HTTP_HOST;
		}

		return null;
	}

	/**
	 * Get client ip
	 *
	 * @return string|boolean
	 */
	public function getClientIp()
	{
		return $this->getServer('HTTP_X_FORWARDED_FOR', $this->getServer('HTTP_X_REAL_IP', $this->getServer('REMOTE_ADDR', false)));

		/* alte version */
		if (!empty($host))
		{
			return $host;
		}
		// Apache
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		// Nginx
		if (isset($_SERVER['HTTP_X_REAL_IP']))
		{
			return $_SERVER['HTTP_X_REAL_IP'];
		}

		// Direkte Verbindung
		if (isset($_SERVER['REMOTE_ADDR']))
		{
			return $_SERVER['REMOTE_ADDR'];
		}

		return false;
	}

	/**
	 * Set params from URL
	 * Similar to $_GET
	 *
	 * @param array $params Parameters
	 *
	 * @return void
	 * @deprecated Gerne zur Diskussion für Refactoring / Mardl
	 *             ersetzt durch setRoute
	 */
	public function setParams(array $params)
	{
		$this->setRoute($params);
	}

	/**
	 * Set Route params from URL
	 * @param array $params
	 * @return void
	 */
	public function setRoute(array $params)
	{
		$this->routeParams = $params;
	}

	/**
	 * Get all param from Url
	 *
	 * @return array
	 * @deprecated Gerne zur Diskussion für Refactoring / Mardl
	 */
	public function getParams()
	{
		return $this->getRoute();
	}

	/**
	 * Get all Route param from Url
	 * @return mixed
	 */
	public function getRoute()
	{
		return $this->routeParams;
	}

	/**
	 * Get param from Url
	 * Similar to $_GET
	 *
	 * @param string $key     Key
	 * @param mixed  $default Default value
	 *
	 * @return string
	 * @deprecated Gerne zur Diskussion für Refactoring / Mardl
	 *             ersetzt durch getRouteParam
	 */
	public function getParam($key, $default = null)
	{
		return $this->getRouteParam($key, $default);

		return isset($this->params[$key]) ? trim($this->params[$key]) : $default;
	}

	/**
	 * Get param from Url with $key
	 *
	 * @param string $key
	 * @param null   $default
	 * @return null|string
	 */
	public function getRouteParam($key, $default = null)
	{
		return isset($this->routeParams[$key]) ? trim($this->routeParams[$key]) : $default;
	}

	/**
	 * Get variable from $_GET and trim it
	 *
	 * @param string $key     Key
	 * @param mixed  $default Default value
	 *
	 * @return string
	 * @deprecated Gerne zur Diskussion für Refactoring / Mardl
	 */
	public function get($key, $default = null)
	{
		return $this->getParamIfExist($key, $default);

		/* oldschool, SuperGlobale werden nicht verwendet. */
		if (!isset($_GET[$key]))
		{
			return $default;
		}
		if (is_scalar($_GET[$key]))
		{
			return trim($_GET[$key]);
		}
		array_walk_recursive($_GET[$key], 'trim');

		return $_GET[$key];
	}

	/**
	 * Get variable from $_POST and trim it
	 *
	 * @param string $key     Key
	 * @param mixed  $default Default value
	 *
	 * @return string
	 * @deprecated Gerne zur Diskussion für Refactoring / Mardl
	 */
	public function post($key, $default = null)
	{
		return $this->getPostIfExist($key, $default);

		/* oldschool, SuperGlobale werden nicht verwendet. */
		if (!isset($_POST[$key]))
		{
			return $default;
		}
		if (is_scalar($_POST[$key]))
		{
			return trim($_POST[$key]);
		}
		array_walk_recursive($_POST[$key], 'trim');

		return $_POST[$key];
	}

	/**
	 * Get variable from $_REQUEST and trim it
	 *
	 * @param string $key     Key
	 * @param mixed  $default Default value
	 *
	 * @return string
	 * @deprecated Gerne zur Diskussion für Refactoring / Mardl
	 *
	 */
	public function request($key, $default = null)
	{
		$ret = $this->getParamIfExist($key, $default);

		return $this->getPostIfExist($key, $ret);


		if (!isset($_REQUEST[$key]))
		{
			return $default;
		}
		if (is_scalar($_REQUEST[$key]))
		{
			return trim($_REQUEST[$key]);
		}
		array_walk_recursive($_REQUEST[$key], 'trim');

		return $_REQUEST[$key];
	}

	/**
	 * Get variable from $_FILES
	 *
	 * @param string $key     Key
	 * @param mixed  $default Default value
	 *
	 * @return string
	 */
	public function files($key, $default = null)
	{
		if (!isset($_FILES[$key]['error']))
		{
			return $default;
		}
		if ($_FILES[$key]['error'] == UPLOAD_ERR_NO_FILE)
		{
			return $default;
		}

		return $_FILES[$key];
	}
}
