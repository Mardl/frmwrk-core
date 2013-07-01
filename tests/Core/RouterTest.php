<?php
/**
 * Unittest für Core\Router
 *
 * PHP version 5.3
 *
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace tests\Core;

use Core\Router;

/**
 * Router test case.
 * 
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Beinhaltet einen Instanz von Core\Router
	 * 
	 * @var Router
	 */
	private $Router;
	
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
	 * 
	 * @return void
	 */
	public function __construct()
	{
		
	}
	
	/**
	 * Tests Router->offsetGet()
	 * 
	 * @return void
	 */
	public function testOffsetGet()
	{
		
		/**
		 * @todo RouterTest: offsetGet test not implemented
		 */
		
	}
	
	/**
	 * Tests Router->getRoute()
	 * 
	 * @return void
	 */
	public function testGetRoute()
	{
		$routes = array( 
			array(
				'key' => 'login',
				'path' => '/login',
				'defaults' => array(
					'module' => 'index',
					'controller' => 'index',
					'action' => 'login',
					'prefix' => '',
					'format' => 'html'
				)
			),
			array(
				'key' => 'logout',
				'path' => '/logout',
				'defaults' => array(
					'module' => 'index',
					'controller' => 'index',
					'action' => 'logout',
					'prefix' => '',
					'format' => 'html'
				)
			),
			array(
				'key' => 'default',
				'path' => '/:module/:controller/:action',
				'defaults' => array(
					'module' => 'index',
					'controller' => 'index',
					'action' => 'index',
					'prefix' => '',
					'format' => 'html'
				)
			)
		);
		
		$router = new Router();
		$router->addRoutes($routes);
		
		$this->assertInstanceOf('Core\Route', $router->getRoute('logout'));
	
	}
	
	/**
	 * Tests Router->addRoute()
	 * 
	 * @return void
	 */
	public function testAddRoute()
	{
		/**
		 * @todo RouterTest: addRoute test not implemented
		 */
		
	}
	
	/**
	 * Tests Router->addRoutes()
	 * 
	 * @return void
	 */
	public function testAddRoutes()
	{
		/**
		 * @todo RouterTest: addRoutes test not implemented
		 */
	
	}
	
	/**
	 * Tests Router->searchRoute()
	 * 
	 * @return void
	 */
	public function testSearchRoute()
	{
		$routes = array( 
			array(
				'key' => 'login',
				'path' => '/login',
				'defaults' => array(
					'module' => 'index',
					'controller' => 'index',
					'action' => 'login',
					'prefix' => '',
					'format' => 'html'
				)
			),
			array(
				'key' => 'default',
				'path' => '/logout',
				'defaults' => array(
					'module' => 'index',
					'controller' => 'index',
					'action' => 'logout',
					'prefix' => '',
					'format' => 'html'
				)
			)
		);
		
		$router = new Router();
		$router->addRoutes($routes);
		
		$this->assertFalse($router->searchRoute('/admin/test/test/index.json'));
	
	}
	
	/**
	 * Tests Router->setParam()
	 * 
	 * @return void
	 */
	public function testSetParam()
	{
		$router = new Router();
		$router->setParam('test', 'value');
		
		$this->assertEquals('value', $router->getParam('test'));
	
		$this->assertNull($router->getParam('blabla'));
		
	}
	
	

}

