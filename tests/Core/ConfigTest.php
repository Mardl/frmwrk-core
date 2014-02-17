<?php
/**
 * Unittest für Core\Config
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
 * Config test case.
 * 
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Beinhaltet eine Instanz von Core\Config
	 *
	 * @var Core\Config
	 */
	private $Config;
	
	/**
	 * Prepares the environment before running a test.
	 * 
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		
		// TODO Auto-generated ConfigTest::setUp()
		
		$this->Config = new Config(/* parameters */);
	
	}
	
	/**
	 * Cleans up the environment after running a test.
	 * 
	 * @return void
	 */
	protected function tearDown()
	{
		// TODO Auto-generated ConfigTest::tearDown()
		
		$this->Config = null;
		
		parent::tearDown();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		// TODO Auto-generated constructor
	}
	
	/**
	 * Tests Config->add()
	 *
	 * Nichts passiert, da die Funktion keinen Rückgabewert hat.
	 * 
	 * @return void
	 */
	public function testAddEXPVoid01()
	{
		$this->Config->add('general', 'general.php');
	
	}
	
	/**
	 * Tests Config->load()
	 * Hier wird eine InvalidArgumentException erwartet da 
	 * über CLI $_SERVER['HTTP_HOST'] nicht gesetzt wird
	 * 
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Unbekannter Hostname
	 * 
	 * @return void
	 */
	public function testLoadEXPExceptionHostNotFound()
	{
		$this->Config->load();
	}
	
	/**
	 * Tests Config->load()
	 * Hier wird eine LengthException erwartet da für den Hostname lifemeter.testing.dev, der
	 * manuell in $_SERVER['HTTP_HOST'] eingetragen wird, keine Konfigurationseintrag vorhanden
	 * ist
	 * 
	 * @expectedException \LengthException
	 * @expectedExceptionMessage Keine Konfiguration für den Host .lifemeter.testing.dev gefunden
	 * 
	 * @return void
	 */
	public function testLoadEXPExceptionConfigNotFound()
	{
		$_SERVER['HTTP_HOST'] = 'lifemeter.testing.dev';
		$this->Config->add('general', 'general.php');
		$this->Config->load();
	
	}
	
	/**
	 * Tests Config->load()
	 * Hier wird erwartet, dass keine Fehler auftreten.
	 *
	 * @return void
	 */
	public function testLoadEXPVoid()
	{
		$_SERVER['HTTP_HOST'] = 'lifemeter.testing.dev';
		$this->Config->add('general', 'general.php');
		$this->Config->add('lifemeter.testing.dev', 'unittests.php');
		$this->Config->load();
		
	}
	
	/**
	 * Tests Config->load()
	 * Basierend auf "testLoadEXPVoid" wird hier erwartet das die globale Konstanten
	 * UNITTESTING mit dem Wert 'defined' aus der "general.php" definiert wurde
	 * 
	 * @return void
	 */
	public function testDefinedEXPDefinedConstantFromGeneral()
	{
		$_SERVER['HTTP_HOST'] = 'lifemeter.testing.dev';
		$this->Config->add('general', 'general.php');
		$this->Config->add('lifemeter.testing.dev', 'unittests.php');
		$this->Config->load();
		$this->assertTrue(defined('UNITTESTING'));
		$this->assertEquals(UNITTESTING, 'defined');
	}
	
	/**
	 * Tests Config->load()
	 * Basierend auf "testLoadEXPVoid" wird hier erwartet das die globale Konstanten
	 * BASE_URL mit dem Wert 'http://lifemeter.testing.dev/' aus der "unittests.php"
	 * definiert wurde
	 *
	 * @return void
	 */
	public function testDefinedEXPDefinedConstantsFromUnittests()
	{
		$_SERVER['HTTP_HOST'] = 'lifemeter.testing.dev';
		$this->Config->add('general', 'general.php');
		$this->Config->add('lifemeter.testing.dev', 'unittests.php');
		$this->Config->load();
		$this->assertTrue(defined('BASE_URL'));
		$this->assertEquals('http://lifemeter.testing.dev/', BASE_URL);
	}
	
	/**
	 * Tests Config->__get()
	 * Hier wird erwartet das Config ein Attribut UNITTESTS_ARRAY mit dem Wert array['defined'] 
	 * besitzt, da diese Konfigurationseinstellung in "unittests.php" definiert wurde 
	 * und ein Array ist
	 * 
	 * @covers Core\Config::__get
	 * 
	 * @return void
	 */
	public function testDefinedEXPDefinedVariableFromUnittests()
	{
		$_SERVER['HTTP_HOST'] = 'lifemeter.testing.dev';
		$this->Config->add('general', 'general.php');
		$this->Config->add('lifemeter.testing.dev', 'unittests.php');
		$this->Config->load();
		$variant = $this->Config->UNITTESTS_ARRAY;
		$this->assertTrue(is_array($variant));
		$this->assertEquals('defined', $variant[0]);
	}
	
	/**
	 * Tests Config->__get()
	 * Hier wird erwartet, dass der Aufruf NULL zurückliefert
	 *
	 * @covers Core\Config::__get
	 * 
	 * @return void
	 */
	public function testDefinedEXPUndefinedVariableFromUnittests()
	{
		$_SERVER['HTTP_HOST'] = 'lifemeter.testing.dev';
		$this->Config->add('general', 'general.php');
		$this->Config->add('lifemeter.testing.dev', 'unittests.php');
		$this->Config->load();
		$this->assertNull($this->Config->UNITTESTS_UNDEFINED);
	}

	/**
	 * Tests private _hostEndsWith
	 * Hier wird erwartet, dass true zurückgeliefert wird
	 * 
	 * @covers Core\Config::_hostEndsWith
	 * 
	 * @return void
	 */
	public function testHostEndsWithEXPTrue()
	{
		$_SERVER['HTTP_HOST'] = 'lifemeter.testing.dev';
		$this->Config->add('general', 'general.php');
		$this->Config->add('lifemeter.testing.dev', 'unittests.php');
		$this->Config->load();
		$reflector = new \ReflectionMethod('Core\Config', '_hostEndsWith');
		$reflector->setAccessible(true);
		$this->assertTrue(
			$reflector->invoke(
				$this->Config, 
				'.'.trim($_SERVER['HTTP_HOST'], '.'),
				'lifemeter.testing.dev'
			)
		);
	}
	
	/**
	 * Tests private _hostEndsWith
	 * Hier wird erwartet, dass false zurückgeliefert wird
	 *
	 * @covers Core\Config::_hostEndsWith
	 * 
	 * @return void
	 */
	public function testHostEndsWithEXPFalse()
	{
		$_SERVER['HTTP_HOST'] = 'lifemeter.testing.dev';
		$this->Config->add('general', 'general.php');
		$this->Config->add('lifemeter.testing.dev', 'unittests.php');
		$this->Config->load();
		$reflector = new \ReflectionMethod('Core\Config', '_hostEndsWith');
		$reflector->setAccessible(true);
		$this->assertFalse(
			$reflector->invoke(
				$this->Config, 
				'.'.trim($_SERVER['HTTP_HOST'], '.'),
				'lifemeter.testing.com'
			)
		);
	}
	
}

