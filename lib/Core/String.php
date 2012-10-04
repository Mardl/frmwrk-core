<?php
/**
 * Core\String-Class
 *
 * PHP version 5.3
 *
 * @category Controller
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */

namespace Core;

/**
 * String
 *
 * @category Controller
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class String
{

	/**
     * Sluggify string
     * Replace non ASCII chars from string
     *
	 * @param string $string String to sluggify
	 *
	 * @return string
	 */
	public static function slug($string)
	{
		$replace = array(
			'ä' => 'ae',
			'ö' => 'oe',
			'ü' => 'ue',
			'é' => 'e',
			'à' => 'a',
			'ß' => 'ss'
		);
		$string = strtr($string, $replace);

        // Replace other chars to -
		$string = preg_replace('/[^a-zA-Z0-9]+/', '-', $string);
		$string = trim($string, '-');
		if ($string == '')
		{
			return '-';
		}
		return $string;
	}

    /**
     * String starts with $needle
     *
     * @param string $haystack Zeichenkette die geprüft werden soll
     * @param string $needle   Zeichenkette auf die geprüft werden soll
     *
     * @return boolean
     */
	public static function startsWith($haystack, $needle)
	{
		return substr($haystack, 0, strlen($needle)) == $needle;
	}

    /**
     * String ends with needle
     *
     * @param string $haystack Zeichenkette die geprüft werden soll
     * @param string $needle   Zeichenkette auf die geprüft werden soll
     *
     * @return boolean
     */
	public static function endsWith($haystack, $needle)
	{
		return substr($haystack, -strlen($needle)) == $needle;
	}

	public static function bcryptEncode($pwd, $salt)
	{
		$salt = '$6$rounds=6000$'.substr($salt,0,16).'$';
		return crypt($pwd, $salt);
	}

	public static function bcryptCheckup($pwd, $stored)
	{
		return (crypt($pwd, $stored) == $stored);
	}
}
