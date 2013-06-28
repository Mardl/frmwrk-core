<?php

namespace Core\Cache;

/**
 * Class Apc
 *
 * @category Core
 * @package  Core\Cache
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class Apc
{

	private static $instance = null;
	private static $instancetime = null;

	/**
	 * @return Apc|null
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Konstruktor
	 */
	private function __construct()
	{
		$this->ttl = ini_get('max_execution_time');
	}

	/**
	 * @param string $key
	 * @param mixed  $val
	 * @return array|bool
	 */
	public function add($key, $val)
	{
		return \apc_store($key, $val, 180);
	}

	/**
	 * @param string $key
	 * @return bool|mixed
	 */
	public function get($key)
	{
		$success = false;
		$val = \apc_fetch($key, $success);
		if ($success)
		{
			return $val;
		}

		return false;
	}

	/**
	 * @param string $key
	 * @return bool|\string[]
	 */
	public function has($key)
	{
		return \apc_exists($key);
	}

	/**
	 * @param string $key
	 * @return bool|\string[]
	 */
	public function remove($key)
	{
		return \apc_delete($key);
	}

	/**
	 * @return array
	 */
	public function info()
	{
		$info = \apc_sma_info();

		return array(
			round((($info['seg_size'] / 1024) / 1024)) . ' MB',
			round((($info['avail_mem'] / 1024) / 1024)) . ' MB',
			round(((($info['seg_size'] - $info['avail_mem']) / 1024) / 1024)) . ' MB'
		);
	}
}
