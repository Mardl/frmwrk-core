<?php
/**
 * Unittest für Core\SystemMessages
 *
 * PHP version 5.3
 *
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace tests\Core;

use Core\SystemMessages;

/**
 * SystemMessages test case.
 * 
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class SystemMessagesTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Prepares the environment before running a test.
	 * Weil SystemMessages ein Singleton ist muss die Instanz immer am Anfang geleert werden.
	 * Dadurch entfällt die notwendigkeit des Teardowns
	 * 
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		
		SystemMessages::clear();
	
	}
	
	/**
	 * Constructs the test case.
	 * 
	 * @return void
	 */
	public function __construct()
	{
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests SystemMessages::add()
	 * 
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Invalid category
	 * 
	 * @return void
	 */
	public function testAddEXPInvalidArgumentException()
	{
		SystemMessages::add('Any Content', 'Invalid Category');
	}
	
	/**
	 * Tests SystemMessages::add()
	 * Hier wird nichts erwartet, da hier kein Fehler auftreten darf.
	 * 
	 * @return void
	 */
	public function testAddEXPVoid()
	{
		SystemMessages::add('Any Content');
	}
	
	/**
	 * Tests SystemMessages::getList()
	 * Hier wird den SystemMessages ein Eintrag hinzugefügt und danach SystemMessages::getList()
	 * ausgeführt.
	 * Erwartet wird ein Array mit nur einem Eintrag
	 * 
	 * @return void
	 */
	public function testGetListEXPSingleArray()
	{
		SystemMessages::add('Any Content');
		$messages = SystemMessages::getList();
		$this->assertEquals(1, count($messages));
		
	}
	
	/**
	 * Tests SystemMessages::getList()
	 * Hier wird den SystemMessages drei Einträge hinzugefügt und danach SystemMessages::getList()
	 * ausgeführt.
	 * Erwartet wird ein Array mit drei Einträgen
	 *
	 * @return void
	 */
	public function testGetListEXPMultiArray()
	{
		$random = rand(2, 200);
		for ($i = 1; $i <= $random; $i++)
		{
			SystemMessages::add("Any Content random: $i");
		}
		$messages = SystemMessages::getList();
		$this->assertEquals($random, count($messages));
	
	}
	
	/**
	 * Tests SystemMessages::clear()
	 * Hier wird den SystemMessages mehrere Einträge hinzugefügt und danach 
	 * SystemMessages::clear() ausgeführt.
	 * SystemMessages::getList() muss hierbei ein leere Array zurückliefern
	 * 
	 * @return void
	 */
	public function testClear()
	{
		$random = rand(1, 200);
		for ($i = 1; $i <= $random; $i++)
		{
			SystemMessages::add("Any Content random: $i");
		}
		SystemMessages::clear();
		$messages = SystemMessages::getList(); 
		$this->assertTrue(empty($messages));
		
	}
	
	
	/**
	 * Tests SystemMessages::addNotice()
	 * Hier wird eine Notiz angefügt und SystemMessages::getList() ausgeführt.
	 * Erwartet wird ein einzeiliges Array mit folgenden Inhalten
	 * ['category']  => 'notice',
	 * ['content']   => 'Dies ist eine Notiz',
	 * ['arguments'] => array(),
	 * ['html']      => false
	 * 
	 * @return void
	 */
	public function testAddNotice()
	{
		SystemMessages::addNotice('Dies ist eine Notiz');
		$messages = SystemMessages::getList();

		$key = array_shift(array_keys($messages));

		$this->assertEquals(1, count($messages));
		$this->assertEquals('notice', $messages[$key]['category']);
		$this->assertEquals('Dies ist eine Notiz', $messages[$key]['content']);
		$this->assertTrue(is_array($messages[$key]['arguments']));
		$this->assertTrue(empty($messages[$key]['arguments']));
		$this->assertFalse($messages[$key]['html']);
		
	}
	
	/**
	 * Tests SystemMessages::addWarning()
	 * Hier wird eine Warning angefügt und SystemMessages::getList() ausgeführt.
	 * Erwartet wird ein einzeiliges Array mit folgenden Inhalten
	 * ['category']  => 'warning',
	 * ['content']   => 'Dies ist ein Warning',
	 * ['arguments'] => array(),
	 * ['html']      => false
	 *
	 * @return void
	 */
	public function testAddWarning()
	{
		SystemMessages::addWarning('Dies ist ein Warning');
		$messages = SystemMessages::getList();

		$key = array_shift(array_keys($messages));

		$this->assertEquals(1, count($messages));
		$this->assertEquals('warning', $messages[$key]['category']);
		$this->assertEquals('Dies ist ein Warning', $messages[$key]['content']);
		$this->assertTrue(is_array($messages[$key]['arguments']));
		$this->assertTrue(empty($messages[$key]['arguments']));
		$this->assertFalse($messages[$key]['html']);
	
	}
	
	/**
	 * Tests SystemMessages::addSuccess()
	 * Hier wird eine Erfolgsmeldung angefügt und SystemMessages::getList() ausgeführt.
	 * Erwartet wird ein einzeiliges Array mit folgenden Inhalten
	 * ['category']  => 'success',
	 * ['content']   => 'Dies ist eine Erfolgsmeldung',
	 * ['arguments'] => array(),
	 * ['html']      => false
	 *
	 * @return void
	 */
	public function testAddSuccess()
	{
		SystemMessages::addSuccess('Dies ist eine Erfolgsmeldung');
		$messages = SystemMessages::getList();

		$key = array_shift(array_keys($messages));

		$this->assertEquals(1, count($messages));
		$this->assertEquals('success', $messages[$key]['category']);
		$this->assertEquals('Dies ist eine Erfolgsmeldung', $messages[$key]['content']);
		$this->assertTrue(is_array($messages[$key]['arguments']));
		$this->assertTrue(empty($messages[$key]['arguments']));
		$this->assertFalse($messages[$key]['html']);
	
	}
	
	/**
	 * Tests SystemMessages::addError()
	 * Hier wird eine Fehlermeldung angefügt und SystemMessages::getList() ausgeführt.
	 * Erwartet wird ein einzeiliges Array mit folgenden Inhalten
	 * ['category']  => 'error',
	 * ['content']   => 'Dies ist eine Fehlermeldung',
	 * ['arguments'] => array(),
	 * ['html']      => false
	 *
	 * @return void
	 */
	public function testAddError()
	{
		SystemMessages::addError('Dies ist eine Fehlermeldung');
		$messages = SystemMessages::getList();

		$key = array_shift(array_keys($messages));

		$this->assertEquals(1, count($messages));
		$this->assertEquals('error', $messages[$key]['category']);
		$this->assertEquals('Dies ist eine Fehlermeldung', $messages[$key]['content']);
		$this->assertTrue(is_array($messages[$key]['arguments']));
		$this->assertTrue(empty($messages[$key]['arguments']));
		$this->assertFalse($messages[$key]['html']);
	
	}
	

}

