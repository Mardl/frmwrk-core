<?php
/**
 * Unittest für Core\String
 *
 * PHP version 5.3
 *
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace tests\Core;

use Core\String;

/**
 * String test case.
 * 
 * Da String nur eine Sammlung von statischen Stringoperationen ist, besteht keine Notwendigkeit
 * von setUp() und tearDown()
 * 
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class StringTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		// Nothing to do
	}
	
	/**
	 * Tests String::slug()
	 * Input:  abcdefghijklmnopqrstuvwxyz0123456789
	 * Output: abcdefghijklmnopqrstuvwxyz0123456789
	 * 
	 * @return void
	 */
	public function testSlugEXPEquals()
	{
		$input = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$this->assertEquals($input, String::slug($input));
	}
	
	/**
	 * Tests String::slug()
	 * Hier wird erwartet, dass der Input nicht dem Output entspricht
	 * Input:  
	 * 		abcdefghijklmnopqrstuvwxyz0123456789ä
	 * 		abcdefghijklmnopqrstuvwxyz0123456789ö
	 * 		abcdefghijklmnopqrstuvwxyz0123456789ü
	 * 		abcdefghijklmnopqrstuvwxyz0123456789ß
	 *
	 * @return void
	 */
	public function testSlugEXPNotEquals()
	{
		$input = 'abcdefghijklmnopqrstuvwxyz0123456789ä';
		$this->assertNotEquals($input, String::slug($input));
		
		$input = 'abcdefghijklmnopqrstuvwxyz0123456789ö';
		$this->assertNotEquals($input, String::slug($input));
		
		$input = 'abcdefghijklmnopqrstuvwxyz0123456789ü';
		$this->assertNotEquals($input, String::slug($input));
		
		$input = 'abcdefghijklmnopqrstuvwxyz0123456789ß';
		$this->assertNotEquals($input, String::slug($input));
	}
	
	/**
	 * Tests String::slug()
	 * Hier wird erwartet, dass Umlaute umgewandelt werden
	 * Input:  
	 * 	ä => ae
	 *  ö => oe
	 *  ü => ue
	 *  ß => ss
	 *
	 * @return void
	 */
	public function testSlugEXPUmlaute()
	{
		$this->assertEquals('ae', String::slug('ä'));
		$this->assertEquals('oe', String::slug('ö'));
		$this->assertEquals('ue', String::slug('ü'));
		$this->assertEquals('ss', String::slug('ß'));
	}
	
	/**
	 * Tests String::slug()
	 * Hier wird erwartet, dass Sonderzeichen in Minus umgewandelt werden
	 * Input:  a!b"c§d$e%f&g/h(i)j=k?l,m.n;n:p#q'r\*s+t@u>v<w€x°y^z_0
	 * Output: a-b-c-d-e-f-g-h-i-j-k-l-m-n-n-p-q-r-s-t-u-v-w-x-y-z-0
	 *
	 * @return void
	 */
	public function testSlugEXPSonderzeichen()
	{
		$input = "a!b\"c§d\$e%f&g/h(i)j=k?l,m.n;n:p#q'r*s+t@u>v<w€x°y^z_0 1";
		$output = "a-b-c-d-e-f-g-h-i-j-k-l-m-n-n-p-q-r-s-t-u-v-w-x-y-z_0-1";
		
		$this->assertEquals($output, String::slug($input));
		
	}
	
	/**
	 * Tests String::slug()
	 * Hier wird erwartet, ein leerer String zu einem Minus wird
	 * Input:  
	 * Output: -
	 *
	 * @return void
	 */
	public function testSlugEXPEmpty()
	{
		$input = "";
		$output = "-";
	
		$this->assertEquals($output, String::slug($input));
	
	}
	
	/**
	 * Tests String::startsWith()
	 * Input 1:
	 * 	Haystack: Dies ist ein Test
	 *  Needle:   Dies
	 *  
	 * Input 2:
	 *  Haystack: http://www.dreiwerken.de
	 *  Needle:   http://
	 * 
	 * @return void
	 */
	public function testStartsWithEXPTrue()
	{
		$this->assertTrue(String::startsWith('Dies ist ein Test', 'Dies'));
		$this->assertTrue(String::startsWith('http://www.dreiwerken.de', 'http://'));
	}
	
	/**
	 * Tests String::startsWith()
	 * Input 1:
	 * 	Haystack: Dies ist ein Test
	 *  Needle:   Diesist
	 *  
	 * Input 2:
	 *  Haystack: http://www.dreiwerken.de
	 *  Needle:   https://
	 *  
	 * Input 3:
	 * 	Haystack: Dies ist ein Test
	 *  Needle:   dies
	 * 
	 * @return void
	 */
	public function testStartsWithEXPFalse()
	{
		$this->assertFalse(String::startsWith('Dies ist ein Test', 'Diesist'));
		$this->assertFalse(String::startsWith('http://www.dreiwerken.de', 'https://'));
		$this->assertFalse(String::startsWith('Dies ist ein Test', 'dies'));
	}
	
	/**
	 * Tests String::endsWith()
	 * 
	 * Input 1:
	 * 	Haystack: Dies ist ein Test
	 *  Needle:   Test
	 *  
	 * Input 2:
	 *  Haystack: http://www.dreiwerken.de
	 *  Needle:   .de
	 * 
	 * @return void
	 */
	public function testEndsWithEXPTrue()
	{
		$this->assertTrue(String::endsWith('Dies ist ein Test', 'Test'));
		$this->assertTrue(String::endsWith('http://www.dreiwerken.de', '.de'));
	
	}
	
	/**
	 * Tests String::endsWith()
	 * Input 1:
	 * 	Haystack: Dies ist ein Test
	 *  Needle:   Diesist
	 *
	 * Input 2:
	 *  Haystack: http://www.dreiwerken.de
	 *  Needle:   .com
	 *
	 * Input 3:
	 * 	Haystack: Dies ist ein Test
	 *  Needle:   test
	 *
	 * @return void
	 */
	public function testEndsWithEXPFalse()
	{
		$this->assertFalse(String::endsWith('Dies ist ein Test', 'Diesist'));
		$this->assertFalse(String::endsWith('http://www.dreiwerken.de', 'https://'));
		$this->assertFalse(String::endsWith('Dies ist ein Test', 'dies'));
	}

}

