<?php
/**
 * Core\Cli-Class
 *
 * PHP version 5.3
 *
 * @category Helper
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace Core;

class Cli
{

	public function __construct(){
		$this->params = $this->_parseParameters();
		define('DISABLE_HTTP', true);
	}

	public function getParams()
	{
		return $this->params;
	}

	public function has($key)
	{
		if (empty($this->params) || !array_key_exists($key, $this->params))
		{
			return false;
		}

		return true;
	}

	public function get($key)
	{
		if (empty($this->params) || !array_key_exists($key, $this->params))
		{
			return false;
		}

		return $this->params[$key];
	}

	public function is($key, $check)
	{
		if (empty($this->params) || !array_key_exists($key, $this->params))
		{
			return false;
		}

		return $this->params[$key] == $check;
	}


	private function _parseParameters()
	{
		$params = array();

		foreach ($_SERVER['argv'] as $key => $value){
			if ($key > 0){
				$parsed  = $this->_parseParameter(trim($value,'--'));
				$params[strtolower($parsed[0])] = $parsed[1];
			}
		}

		return $params;

	}

	private function _parseParameter($element)
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
?>