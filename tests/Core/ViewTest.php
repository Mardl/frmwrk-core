<?php
/**
 * Unittest für Core\View
 *
 * PHP version 5.3
 *
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace tests\Core;

use Core\View,
	Core\Request,
	Core\Response,
	Core\Router,
	Core\HTMLHelper,
	jamwork\common\Registry;

/**
 * View test case.
 * 
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class ViewTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Beinhaltet die Instanz von Core\View
	 *
	 * @var View
	 */
	private $View;
	
	/**
	 * Prepares the environment before running a test.
	 * 
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		
		$reg = Registry::getInstance();
		
		//Request
		$request = new Request($_GET, $_POST, $_SERVER, $_COOKIE);
		$reg->setRequest($request);
		
		//Response
		$response = new Response();
		$response->addHeader('Content-Type', 'text/html; charset=utf-8');
		$reg->setResponse($response);
		
		//Router
		$router = new Router();
		$reg->router = $router;
		
		$this->View = new View(/* parameters */);
		$this->View->html = new HTMLHelper();
	}
	
	/**
	 * Cleans up the environment after running a test.
	 * 
	 * @return void
	 */
	protected function tearDown()
	{
		$this->View = null;
		
		parent::tearDown();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		/**
		 * Nothing to do
		 */
	}
	
	/**
	 * Tests View->__construct()
	 * 
	 * @covers Core\View::__construct
	 * 
	 * @return void
	 */
	public function testConstructEXPOkNoTemplateSet()
	{
		$this->View = new View();
	}
	
	/**
	 * Tests View->__construct()
	 *
	 * @covers Core\View::__construct
	 *
	 * @return void
	 */
	public function testConstructEXPOkTemplateSet()
	{
		$this->View = new View(__DIR__.'/../TestClasses/testtemplate.html.php');
	}
	
	/**
	 * Tests View->__toString()
	 * 
	 * @covers Core\View::__toString
	 * @covers Core\View::render
	 * 
	 * @return void
	 */
	public function testToStringEXPOk()
	{
		$this->View = new View(__DIR__.'/../TestClasses/testtemplate.html.php');
		$this->assertEquals('ok', $this->View->__toString());
	}
	
	/**
	 * Tests View->__toString()
	 *
	 * @covers Core\View::__toString
	 * @covers Core\View::render
	 *
	 * @return void
	 */
	public function testToStringEXPNotOk()
	{
		$this->View = new View(__DIR__.'/../TestClasses/testtemplateException.html.php');
		$this->assertNotEquals('ok', $this->View->__toString());
	}
	
	/**
	 * Tests View->__toString()
	 *
	 * @covers Core\View::__toString
	 * @covers Core\View::render
	 *
	 * @return void
	 */
	public function testToStringEXPOkNoTemplate()
	{
		$this->View = new View();
		$this->assertEquals('', $this->View->__toString());
	}
	
	/**
	 * Tests View->offsetGet()
	 * 
	 * @return void
	 */
	public function testOffsetGetEXPNull()
	{
		$this->assertNull($this->View->offsetGet('testKey'));
	
	}
	
	/**
	 * Tests View->getRoute()
	 * 
	 * @expectedException ErrorException
	 * 
	 * @return void
	 */
	public function testGetRouteEXPExceptionNoDefaultRouteIsSet()
	{
		$this->View->getRoute();
	
	}
	
	/**
	 * Tests View->url()
	 * 
	 * @return void
	 */
	public function testUrl()
	{
		$this->markTestSkipped("url test will not implemented");
		
	}
	
	/**
	 * Tests View->setTitle()
	 * 
	 * @return void
	 */
	public function testSetTitle()
	{
		$title = $this->View->setTitle('first');
		
		$this->assertTrue(is_array($title));
		$this->assertEquals(1, count($title));
	
	}
	
	/**
	 * Tests View->addTitle()
	 * 
	 * @return void
	 */
	public function testAddTitle()
	{
		$title = $this->View->setTitle('first');
		
		$this->assertTrue(is_array($title));
		$this->assertEquals(1, count($title));
	
		$title = $this->View->addTitle('second');
		$this->assertEquals(2, count($title));
	}
	
	/**
	 * Tests View->addTitle()
	 *
	 * @return void
	 */
	public function testSetTitleEXPFalse()
	{
		$title = $this->View->setTitle('first');
	
		$this->assertTrue(is_array($title));
		$this->assertEquals(1, count($title));
	
		$title = $this->View->setTitle('second');
		$this->assertNotEquals(2, count($title));
	}
	
	/**
	 * Tests View->getTitle()
	 * 
	 * @return void
	 */
	public function testGetTitleEXPNoTitleSet()
	{
		$this->assertEquals('', $this->View->getTitle());
	
	}
	
	/**
	 * Tests View->getTitle()
	 * erwarteter Wert:
	 * second - first
	 * 
	 * @return void
	 */
	public function testGetTitleEXPTitleSeparatedByMinus()
	{
		$this->View->setTitle('first');
		$this->View->addTitle('second');
		
		$this->assertEquals('second - first', $this->View->getTitle());
	
	}
	
	/**
	 * Tests View->getTitle()
	 * erwarteter Wert:
	 * second,first
	 *
	 * @return void
	 */
	public function testGetTitleEXPTitleSeparatedByKomma()
	{
		$this->View->setTitle('first');
		$this->View->addTitle('second');
	
		$this->assertEquals('second,first', $this->View->getTitle(','));
	
	}
	
	/**
	 * Tests View->addKeyword()
	 * 
	 * @return void
	 */
	public function testAddKeyword1Keyword()
	{
		$keywords = $this->View->addKeyword('unittest');
		
		$this->assertTrue(is_array($keywords));
		$this->assertEquals(1, count($keywords));
	
	}
	
	/**
	 * Tests View->addKeyword()
	 *
	 * @return void
	 */
	public function testAddKeyword3Keyword()
	{
		$keywords = $this->View->addKeyword('unittest first');
		$keywords = $this->View->addKeyword('unittest second');
		$keywords = $this->View->addKeyword('unittest third');
	
		$this->assertTrue(is_array($keywords));
		$this->assertEquals(3, count($keywords));
	
	}
	
	/**
	 * Tests View->addKeywords() und View->getKeywords()
	 * 
	 * Erwarteter Wert:
	 * unittest first, unittest second, unittest third
	 * 
	 * @covers Core\View::addKeywords
	 * @covers Core\View::getKeywords
	 * 
	 * @return void
	 */
	public function testAddKeywordsEXPOkKeywordListSorted()
	{
		$this->View->addKeywords('unittest first', 'unittest second', 'unittest third');
		$this->assertEquals(
			'unittest first, unittest second, unittest third',
			$this->View->getKeywords()
		);
	}
	
	/**
	 * Tests View->addKeywords() und View->getKeywords()
	 *
	 * Erwarteter Wert:
	 * unittest first, unittest second, unittest third
	 *
	 * @covers Core\View::addKeywords
	 * @covers Core\View::getKeywords
	 *
	 * @return void
	 */
	public function testAddKeywordsEXPNotOkKeywordListSorted()
	{
		$this->View->addKeywords('unittest second', 'unittest first', 'unittest third');
		$this->assertNotEquals(
			'unittest second, unittest first, unittest third',
			$this->View->getKeywords()
		);
		$this->assertEquals(
			'unittest first, unittest second, unittest third',
			$this->View->getKeywords()
		);
	}
	
	/**
	 * Tests View->addKeywords() und View->getKeywords()
	 *
	 * Erwarteter Wert:
	 * false, weil kein Keyword gesetzt
	 *
	 * @covers Core\View::addKeywords
	 * @covers Core\View::getKeywords
	 *
	 * @return void
	 */
	public function testAddKeywordsEXPFalse()
	{
		$this->assertFalse($this->View->getKeywords());
	}
	
	/**
	 * Tests View->setDescription()
	 * 
	 * @covers Core\View::setDescription
	 * @covers Core\View::getDescription
	 * 
	 * @return void
	 */
	public function testSetDescription()
	{
		$desc = 'Dies ist ein Test';
		
		$this->View->setDescription($desc);
		$this->assertEquals($desc, $this->View->getDescription());
	
	}
	
	/**
	 * Tests View->getDescription()
	 * 
	 * @return void
	 */
	public function testGetDescriptionEXPNull()
	{
		$this->assertNull($this->View->getDescription());
	
	}
	
	/**
	 * Tests View->getDescription()
	 *
	 * @return void
	 */
	public function testGetDescriptionEXPShortenedString()
	{
		$desc = 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy 
		eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo 
		duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum 
		dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam 
		erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea 
		takimata sanctus est Lorem ipsum dolor sit amet.';
		
		$this->View->setDescription($desc);
		
		$this->assertEquals(140, strlen($this->View->getDescription()));
	
	}
	
	/**
	 * Tests View->setTemplate()
	 * 
	 * @return void
	 */
	public function testSetTemplateEXPArray1Count()
	{
		$template = $this->View->setTemplate(__DIR__.'/../TestClasses/testtemplate.html.php');
		
		$this->assertTrue(is_array($template));
		$this->assertEquals(1, count($template));
		
		$template = $this->View->setTemplate(__DIR__.'/../TestClasses/testtemplate2.html.php');
		$this->assertEquals(1, count($template));
		$this->assertNotEquals(
			__DIR__.'/../TestClasses/testtemplate.html.php',
			$template[0]
		);
	}
	
	/**
	 * Tests View->addTemplate()
	 * 
	 * @return void
	 */
	public function testAddTemplate()
	{
		$template = $this->View->setTemplate(__DIR__.'/../TestClasses/testtemplate.html.php');
		
		$this->assertTrue(is_array($template));
		$this->assertEquals(1, count($template));
		
		$template = $this->View->addTemplate(__DIR__.'/../TestClasses/testtemplate2.html.php');
		$this->assertEquals(2, count($template));
		$this->assertEquals(
			__DIR__.'/../TestClasses/testtemplate2.html.php',
			$template[1]
		);
	
	}
	
	/**
	 * Tests View->removeTemplates()
	 * 
	 * @return void
	 */
	public function testRemoveTemplates()
	{
		$template = $this->View->setTemplate(__DIR__.'/../TestClasses/testtemplate.html.php');
		$template = $this->View->addTemplate(__DIR__.'/../TestClasses/testtemplate2.html.php');
		$template = $this->View->addTemplate(__DIR__.'/../TestClasses/testtemplate3.html.php');
		
		$template = $this->View->removeTemplates();
		$this->assertEquals(0, count($template));
		
		
	}
	
	/**
	 * Tests View->getTemplates()
	 * 
	 * @return void
	 */
	public function testGetTemplates()
	{
		$this->View->setTemplate(__DIR__.'/../TestClasses/testtemplate.html.php');
		$this->View->addTemplate(__DIR__.'/../TestClasses/testtemplate2.html.php');
		$this->View->addTemplate(__DIR__.'/../TestClasses/testtemplate3.html.php');
		
		$templates = $this->View->getTemplates();
		
		$this->assertTrue(is_array($templates));
		$this->assertEquals(3, count($templates));
		$this->assertEquals(
			__DIR__.'/../TestClasses/testtemplate2.html.php',
			$templates[1]
		);
		
		$this->assertEquals(
			__DIR__.'/../TestClasses/testtemplate.html.php',
			$templates[0]
		);
		
		$this->assertEquals(
			__DIR__.'/../TestClasses/testtemplate3.html.php',
			$templates[2]
		);
	
	}
	
	/**
	 * Tests View->render()
	 * 
	 * @return void
	 */
	public function testRenderEXPOk()
	{
		$this->assertEquals('ok', $this->View->render(__DIR__.'/../TestClasses/testtemplate.html.php'));
	
	}

}

