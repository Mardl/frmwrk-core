<?php

namespace Core\Cache;

/**
 *
 * @author alexjonser
 *
 */
class Apc
{

	private static $instance = null;
	private static $instancetime = null;

	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 */
	private function __construct()
	{
		$this->ttl = ini_get('max_execution_time');
	}

	public function add($key, $val)
	{
		return \apc_store($key, $val, 180);
	}

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

	public function has($key)
	{
		return \apc_exists($key);
	}

	public function remove($key)
	{
		return \apc_delete($key);
	}

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

?>