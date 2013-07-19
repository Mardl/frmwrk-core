<?php

namespace Core;

/**
 * Class ModelInformation
 *
 * @category Core
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class ModelInformation
{

	/**
	 * Messages
	 *
	 * @var array
	 */
	private static $information = array();

	/**
	 * @param string $className
	 * @param string $key
	 * @param mixed  $value
	 * @return void
	 */
	public static function set($className, $key, $value)
	{
		if (!isset(self::$information[$className]))
		{
			self::$information[$className] = array();
		}
		self::$information[$className][$key] = $value;
	}

	/**
	 * @param string $className
	 * @param string $key
	 * @return null
	 */
	public static function get($className, $key)
	{
		//return null;
		if (!isset(self::$information[$className][$key]))
		{
			return null;
		}

		return self::$information[$className][$key];
	}

	/**
	 * Leert den kompletten Cache
	 * @return null
	 */
	public static function clear()
	{
		self::$information = array();
	}
}
