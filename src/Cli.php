<?php

namespace Core;

/**
 * Class Cli
 *
 * @category Core
 * @package  Core
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class Cli
{

	/**
	 * Konstruktor
	 */
	public function __construct()
	{
		$this->params = $this->parseParameters();
		if (!defined('DISABLE_HTTP')) {
			define('DISABLE_HTTP', true);
		}
	}

	/**
	 * @return array
	 */
	public function getParams()
	{
		return $this->params;
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function has($key)
	{
		if (empty($this->params) || !array_key_exists($key, $this->params))
		{
			return false;
		}

		return true;
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function get($key)
	{
		if (empty($this->params) || !array_key_exists($key, $this->params))
		{
			return false;
		}

		return $this->params[$key];
	}

	/**
	 * @param string $key
	 * @param mixed  $check
	 * @return bool
	 */
	public function is($key, $check)
	{
		if (empty($this->params) || !array_key_exists($key, $this->params))
		{
			return false;
		}

		return $this->params[$key] == $check;
	}

	/**
	 * @return array
	 */
	private function parseParameters()
	{
		$params = array();

		foreach ($_SERVER['argv'] as $key => $value)
		{
			if ($key > 0)
			{
				$parsed = $this->parseParameter(trim($value, '--'));
				$params[strtolower($parsed[0])] = $parsed[1];
			}
		}

		return $params;
	}

	/**
	 * @param string $element
	 * @return array
	 */
	private function parseParameter($element)
	{
		$part = explode('=', $element);
		if (count($part) == 2)
		{
			return $part;
		}
		else
		{
			return array($part[0], true);
		}
	}
}
