<?php
/**
 * Unittest für Core\FrontController
 *
 * PHP version 5.3
 *
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace tests\Core;

use Core\FrontController;

/**
 * FrontController test case.
 * 
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class FrontControllerTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Beinhaltet eine Instanz von Core\FrontController
	 * 
	 * @var FrontController
	 */
	private $FrontController;
	
	/**
	 * Prepares the environment before running a test.
	 * 
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		
		// TODO Auto-generated FrontControllerTest::setUp()
		
		// $this->FrontController = new FrontController(/* parameters */);
	
	}
	
	/**
	 * Cleans up the environment after running a test.
	 * 
	 * @return void
	 */
	protected function tearDown()
	{
		// TODO Auto-generated FrontControllerTest::tearDown()
		
		$this->FrontController = null;
		
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
	 * Tests FrontController->__construct()
	 * 
	 * @covers FrontController::__construct
	 * 
	 * @return void
	 */
	public function testConstruct()
	{
		// TODO Auto-generated FrontControllerTest->test__construct()
		$this->markTestIncomplete("__construct test not implemented");
		
		$this->FrontController->__construct(/* parameters */);
	
	}
	
	/**
	 * Tests FrontController->setRouter()
	 * 
	 * @return void
	 */
	public function testSetRouter()
	{
		// TODO Auto-generated FrontControllerTest->testSetRouter()
		$this->markTestIncomplete("setRouter test not implemented");
		
		$this->FrontController->setRouter(/* parameters */);
	
	}
	
	/**
	 * Tests FrontController->addPageToStack()
	 * 
	 * @return void
	 */
	public function testAddPageToStack()
	{
		// TODO Auto-generated FrontControllerTest->testAddPageToStack()
		$this->markTestIncomplete("addPageToStack test not implemented");
		
		$this->FrontController->addPageToStack(/* parameters */);
	
	}
	
	/**
	 * Tests FrontController->render()
	 * 
	 * @return void
	 */
	public function testRender()
	{
		// TODO Auto-generated FrontControllerTest->testRender()
		$this->markTestIncomplete("render test not implemented");
		
		$this->FrontController->render(/* parameters */);
	
	}
	
	/**
	 * Tests FrontController->execute()
	 * 
	 * @return void
	 */
	public function testExecute()
	{
		// TODO Auto-generated FrontControllerTest->testExecute()
		$this->markTestIncomplete("execute test not implemented");
		
		$this->FrontController->execute(/* parameters */);
	
	}
	
	/**
	 * Tests FrontController->dispatchLoop()
	 * 
	 * @return void
	 */
	public function testDispatchLoop()
	{
		// TODO Auto-generated FrontControllerTest->testDispatchLoop()
		$this->markTestIncomplete("dispatchLoop test not implemented");
		
		$this->FrontController->dispatchLoop(/* parameters */);
	
	}
	
	/**
	 * Tests FrontController->searchAction()
	 * 
	 * @return void
	 */
	public function testSearchAction()
	{
		// TODO Auto-generated FrontControllerTest->testSearchAction()
		$this->markTestIncomplete("searchAction test not implemented");
		
		$this->FrontController->searchAction(/* parameters */);
	
	}

}

