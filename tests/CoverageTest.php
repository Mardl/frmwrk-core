<?php
/**
 * Unittest für Coverage
 * Hilfstest, der alle Klasse für jeden Testlauf einmal inistanziert
 *
 * PHP version 5.3
 *
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace tests\Core;

use Core\Config;

/**
 * Coverage test case.
 * 
 * @category Unittest
 * @package  Main
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class Coverage extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		$this->whiteList = array(
			'src'
		);
	}

	/**
	 * Hilfstest ausführen
	 * 
	 * @return void
	 */
	public function testInstances()
	{
		foreach ($this->whiteList as $folder)
		{
			$this->_open($folder);
		}
	
	}
	
	/**
	 * Die Funktion durchläuft den übergebenen Pfade und inkludiert alle Dateien.
	 * 
	 * @param string $dir Pfadangabe
	 * 
	 * @return void
	 */
	private function _open($dir)
	{
		$directory = opendir($dir);
		
		while ( ($file = readdir($directory)) == true )
		{
			if ($file != '.' && $file != '..')
			{
				if (is_dir($dir.'/'.$file))
				{
					$this->_open($dir.'/'.$file);
				}
				else
				{
					include_once $dir.'/'.$file;
				}
				
			}
		}
	}
}

