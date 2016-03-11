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

use Core\MyString;

/**
 * MyString test case.
 * 
 * Da String nur eine Sammlung von statischen Stringoperationen ist, besteht keine Notwendigkeit
 * von setUp() und tearDown()
 * 
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class MyStringTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		// Nothing to do
	}
	
	/**
	 * Tests MyString::slug()
	 * Input:  abcdefghijklmnopqrstuvwxyz0123456789
	 * Output: abcdefghijklmnopqrstuvwxyz0123456789
	 * 
	 * @return void
	 */
	public function testSlugEXPEquals()
	{
		$input = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$this->assertEquals($input, MyString::slug($input));
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
		$this->assertNotEquals($input, MyString::slug($input));
		
		$input = 'abcdefghijklmnopqrstuvwxyz0123456789ö';
		$this->assertNotEquals($input, MyString::slug($input));
		
		$input = 'abcdefghijklmnopqrstuvwxyz0123456789ü';
		$this->assertNotEquals($input, MyString::slug($input));
		
		$input = 'abcdefghijklmnopqrstuvwxyz0123456789ß';
		$this->assertNotEquals($input, MyString::slug($input));
	}
	
	/**
	 * Tests MyString::slug()
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
		$this->assertEquals('ae', MyString::slug('ä'));
		$this->assertEquals('oe', MyString::slug('ö'));
		$this->assertEquals('ue', MyString::slug('ü'));
		$this->assertEquals('ss', MyString::slug('ß'));
	}
	
	/**
	 * Tests MyString::slug()
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
		
		$this->assertEquals($output, MyString::slug($input));
		
	}
	
	/**
	 * Tests MyString::slug()
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
	
		$this->assertEquals($output, MyString::slug($input));
	
	}
	
	/**
	 * Tests MyString::startsWith()
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
		$this->assertTrue(MyString::startsWith('Dies ist ein Test', 'Dies'));
		$this->assertTrue(MyString::startsWith('http://www.dreiwerken.de', 'http://'));
	}
	
	/**
	 * Tests MyMyString::startsWith()
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
		$this->assertFalse(MyString::startsWith('Dies ist ein Test', 'Diesist'));
		$this->assertFalse(MyString::startsWith('http://www.dreiwerken.de', 'https://'));
		$this->assertFalse(MyString::startsWith('Dies ist ein Test', 'dies'));
	}
	
	/**
	 * Tests MyString::endsWith()
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
		$this->assertTrue(MyString::endsWith('Dies ist ein Test', 'Test'));
		$this->assertTrue(MyString::endsWith('http://www.dreiwerken.de', '.de'));
	
	}
	
	/**
	 * Tests MyString::endsWith()
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
		$this->assertFalse(MyString::endsWith('Dies ist ein Test', 'Diesist'));
		$this->assertFalse(MyString::endsWith('http://www.dreiwerken.de', 'https://'));
		$this->assertFalse(MyString::endsWith('Dies ist ein Test', 'dies'));
	}

}

