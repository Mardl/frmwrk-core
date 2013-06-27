<?php
/**
 * Unittest für Core\PublicController
 *
 * PHP version 5.3
 *
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace tests\Core;

use Core\Route,
	Core\Controller,
	jamwork\common\Registry,
	Core\Request,
	Core\Response,
	Core\View,
	Core\Router,
	Core\FrontController;

/**
 * PublicController test case.
 * 
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class PublicControllerTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 *
	 * @var PublicController
	 */
	private $Controller;
	
	/**
	 * Prepares the environment before running a test.
	 * 
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		
	}
	
	/**
	 * Cleans up the environment after running a test.
	 * 
	 * @return void
	 */
	protected function tearDown()
	{
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
	 * Tests PublicController->__construct()
	 * 
	 * @covers Core\PublicController::__construct
	 * @covers Core\Controller::__construct
	 * 
	 * @return void
	 */
	public function testConstruct()
	{
		Registry::getInstance()->view = new View();
		Registry::getInstance()->login = 1;
		
		$this->Controller = new TestController();
	}
	
	/**
	 * testCall
	 * 
	 * @expectedException InvalidArgumentException
	 * 
	 * @return void
	 */
	public function testCall()
	{
		$this->Controller = new TestController();
		
		$this->Controller->hello();
	}
	
	/**
	 * testSetRouter
	 * 
	 * @return void
	 */
	public function testSetRouter()
	{
		$this->Controller = new TestController();
	
		$this->assertTrue($this->Controller->setRouter(Registry::getInstance()->router));
	}

	/**
	 * testSetView 
	 *
	 * @return void
	 */
	public function testSetView()
	{
		$this->Controller = new TestController();
		$this->assertTrue($this->Controller->setView(Registry::getInstance()->view));
		
		$this->Controller->setRouter(Registry::getInstance()->router);
		$this->assertTrue($this->Controller->setView(Registry::getInstance()->view));
	}
	
	/**
	 * testCreateView
	 *
	 * @return void
	 */
	public function testCreateView()
	{
		$this->Controller = new TestController();
		$this->Controller->setRouter(Registry::getInstance()->router);
		$this->assertInstanceOf('Core\View', $this->Controller->createView());
	}
	
	/**
	 * testSetRequest
	 *
	 * @return void
	 */
	public function testSetRequest()
	{
		$this->Controller = new TestController();
	
		$this->assertTrue($this->Controller->setRequest(Registry::getInstance()->getRequest()));
	}
	
	/**
	 * testSetResponse
	 *
	 * @return void
	 */
	public function testSetResponse()
	{
		$this->Controller = new TestController();
	
		$this->assertTrue($this->Controller->setResponse(Registry::getInstance()->getResponse()));
	}
	
	/**
	 * testSetNoRender
	 *
	 * @return void
	 */
	public function testSetNoRender()
	{
		$this->Controller = new TestController();
		$this->Controller->setNoRender();
		$this->assertTrue($this->Controller->isNoRender());
		
		$this->Controller->setNoRender(false);
		$this->assertFalse($this->Controller->isNoRender());
	}
	
	/**
	 * testSetFrontcontroller
	 *
	 * @return void
	 */
	public function testSetFrontcontroller()
	{
		$this->Controller = new TestController();
		$this->Controller->setFrontController(new FrontController());
	}
	
	/**
	 * testSetFrontcontroller
	 * 
	 * @expectedException PHPUnit_Framework_Error
	 *
	 * @return void
	 */
	public function testSetFrontcontrollerEXPException()
	{
		$this->Controller = new TestController();
		$this->Controller->setFrontController();
	}
	
	/**
	 * testForward
	 *
	 * @expectedException PHPUnit_Framework_Error
	 *
	 * @return void
	 */
	public function testForwardEXPException()
	{
		$this->Controller = new TestController();
		$this->Controller->setFrontController(new FrontController());
		$this->Controller->forward();
	}
	
	/**
	 * testForward
	 *
	 * @return void
	 */
	public function testForwardEXPNothing()
	{
		$this->Controller = new TestController();
		$this->Controller->setFrontController(new FrontController());
		$this->Controller->forward('testaction');
	}
	
	/**
	 * testInit
	 *
	 * @return void
	 */
	public function testInitEXPNothing()
	{
		$this->Controller = new TestController();
		$this->Controller->init();
	}
	
}

/**
 * Interne Testklasse zum Testen von Core\Controller
 * 
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class TestController extends Controller
{
	
}
