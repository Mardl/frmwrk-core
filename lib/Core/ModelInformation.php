<?php
/**
 * Core\ModelInformation-Class
 *
 * PHP version 5.3
 *
 * @category Helper
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */

namespace Core;

/**
 * SystemMessages
 *
 * @category Helper
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


	public static function set($className, $key, $value)
	{
		if (!isset(self::$information[$className])){
			self::$information[$className] = array();
		}
		self::$information[$className][$key] = $value;

	}

	public static function get($className, $key){
		//return null;
		if (!isset(self::$information[$className][$key])){
			return null;
		}
		return self::$information[$className][$key];
	}

}
