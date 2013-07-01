<?php
/**
 * Unittest für Core\Route
 *
 * PHP version 5.3
 *
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace tests\Core;

use Core\Route,
	Core\Router,
	Core\Request,
	jamwork\common\Registry;

/**
 * Route test case.
 * 
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Beinhaltet eine Instanz von Core\Route
	 * 
	 * @var Route
	 */
	private $Route;
	
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
		$this->Route = new Route(
			'/:module/:controller/:action',
			array(
				'module' => 'index',
				'controller' => 'index',
				'action' => 'index',
				'prefix' => '',
				'format' => 'html'
			)
		);
	}
	
	/**
	 * Tests Route->__construct()
	 * 
	 * @covers Core\Route::__construct
	 * 
	 * @return void
	 */
	public function testConstruct()
	{
		$this->Route = new Route(
			'/:module/:controller/:action',
			array(
				'module' => 'index',
				'controller' => 'index',
				'action' => 'index',
				'prefix' => '',
				'format' => 'html'
			)
		);
	
	}
	
	/**
	 * Tests Route->getRouter()
	 * 
	 * @return void
	 */
	public function testGetRouter()
	{
		$router = new Router();
		$this->Route->setRouter($router);
		
		$this->assertEquals($router, $this->Route->getRouter());
	
	}
	
	/**
	 * Tests Route->setRouter()
	 * 
	 * @return void
	 */
	public function testSetRouter()
	{
		$this->Route->setRouter(new Router());
	}
	
	
	/**
	 * Tests Route->match()
	 * 
	 * @return void
	 */
	public function testMatchEXPTrueDefaultRoute()
	{
		$router = new Router();
		$this->Route->setRouter($router);
		
		$this->assertTrue(is_array($this->Route->match('http://alex.dreiwerken.de/test/test/index.html')));
	}
	
	/**
	 * Tests Route->match()
	 *
	 * @return void
	 */
	public function testMatchEXPFalse()
	{
		$this->Route = new Route(
			'/admin/:module/:controller/:action',
			array(
				'module' => 'index',
				'controller' => 'index',
				'action' => 'index',
				'prefix' => 'admin_',
				'format' => 'html'
			)
		);
		
		$router = new Router();
		$this->Route->setRouter($router);
	
		$this->assertFalse($this->Route->match('http://alex.dreiwerken.de/test/test/index.html'));
	}
	
	/**
	 * Tests Route->match()
	 *
	 * @return void
	 */
	public function testMatchEXPTrueDefaultRouteWithoutExtension()
	{
		$router = new Router();
		$this->Route->setRouter($router);
	
		$this->assertTrue(is_array($this->Route->match('http://alex.dreiwerken.de/test/test/index')));
	}

	/**
	 * Tests Route->match()
	 *
	 * @return void
	 */
	public function testMatchEXPFalseTooFewArguments()
	{
		$this->Route = new Route(
			'/:module/:controller/user/:action',
			array(
				'module' => 'index',
				'controller' => 'index',
				'action' => 'index',
				'prefix' => '',
				'format' => 'html'
			)
		);
		
		$router = new Router();
		$this->Route->setRouter($router);
	
		$this->assertFalse($this->Route->match('http://alex.dreiwerken.de/test/test/index.html'));
	}
	
	/**
	 * Tests Route->match()
	 *
	 * @return void
	 */
	public function testMatchEXPFalseTooFewArguments2()
	{
		$this->Route = new Route(
			'/:module/:controller/user/:action',
			array(
				'module' => 'index',
				'controller' => 'index',
				'action' => 'index',
				'prefix' => '',
				'format' => 'html'
			)
		);
	
		$router = new Router();
		$this->Route->setRouter($router);
	
		$this->assertFalse($this->Route->match('http://alex.dreiwerken.de/test'));
	}
	
	/**
	 * Tests Route->match()
	 *
	 * @return void
	 */
	public function testUrlNoParams()
	{
		$router = new Router();
		$this->Route->setRouter($router);
	
		$this->assertEquals('/', $this->Route->url(array()));
	}
	
	/**
	 * Tests Route->match()
	 *
	 * @return void
	 */
	public function testUrlWithParams()
	{
		$router = new Router();
		$this->Route->setRouter($router);
	
		$this->assertEquals(
			'/test/test/test.html',
			$this->Route->url(
				array(
					'controller' => 'test',
					'module' => 'test',
					'action' => 'test'
				)
			)
		);
	}
	
	/**
	 * Tests Route->url()
	 *
	 * @return void
	 */
	public function testUrl2()
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
		
		$router->searchRoute('/test/test/test.html');
		
		$this->Route->setRouter($router);
	
		$this->assertEquals(
			'/test/test/test.html',
			$this->Route->url(
				array(
					'controller' => 'test',
					'module' => 'test',
					'action' => 'test'
					)
			)
		);
		
		$this->assertEquals(
			'/index.html',
			$this->Route->url(
				array(
					'controller' => 'index',
					'module' => 'index',
					'action' => 'index',
					'format' => 'html'
				)
			)
		);
		
		$this->assertEquals(
			'/test.html',
			$this->Route->url(
				array(
					'module' => 'test',
					'controller' => 'index',
					'action' => 'index'
				)
			)
		);
		
		$this->assertEquals(
			'/test.json',
			$this->Route->url(
				array(
					'module' => 'test',
					'controller' => 'index',
					'action' => 'index',
					'format' => 'json'
				)
			)
		);
		
		$this->assertEquals(
			'/test.html#testanchor',
			$this->Route->url(
				array(
					'module' => 'test',
					'controller' => 'index',
					'action' => 'index',
					'anchor' => 'testanchor'
				)
			)
		);
		
		$this->assertEquals(
			'/test.html?userid=1',
			$this->Route->url(
				array(
					'module' => 'test',
					'controller' => 'index',
					'action' => 'index',
					'userid' => '1'
				)
			)
		);
	}
	

	/**
	 * Tests Route->url()
	 *
	 * @expectedException ErrorException
	 *
	 * @return void
	 */
	public function testUrlConstantPartsEXPExceptionUserIdIsNotSet()
	{
		$routes = array(
			array(
				'key' => 'user',
				'path' => '/user/:id/:controller/:action',
				'defaults' => array(
					'module' => 'user',
					'controller' => 'index',
					'action' => 'index',
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
	
		$route = $router->searchRoute('/user/1/index.html');
	
		$this->Route = new Route(
			'/user/:id/:controller/:action',
			array(
				'module' => 'index',
				'controller' => 'index',
				'action' => 'index',
				'prefix' => '',
				'format' => 'html'
			)
		);
		
		$this->Route->url(
			array(
				'controller' => 'test',
				'action' => 'test'
			),
			true
		);
	
	}
	
	/**
	 * Tests Route->url()
	 *
	 * @return void
	 */
	public function testUrlConstantParts()
	{
		$routes = array(
			array(
				'key' => 'user',
				'path' => '/user/:id/:controller/:action',
				'defaults' => array(
					'module' => 'user',
					'controller' => 'index',
					'action' => 'index',
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
	
		$route = $router->searchRoute('/user/1/index.html');
	
		$this->Route = new Route(
			'/user/:id/:controller/:action',
			array(
				'module' => 'user',
				'controller' => 'index',
				'action' => 'index',
				'prefix' => '',
				'format' => 'html'
			)
		);
	
		$this->Route->setRouter($router);
		
		$this->assertEquals(
			'/user/1/test/test.html',
			$this->Route->url(
				array(
					'module' => 'user',
					'controller' => 'test',
					'action' => 'test',
					'id' => 1
				),
				false
			)
		);
	
	}
	
	/**
	 * Tests Route->url()
	 *
	 * @return void
	 */
	public function testUrlAbsolute()
	{
		$routes = array(
			array(
				'key' => 'user',
				'path' => '/user/:id/:controller/:action',
				'defaults' => array(
					'module' => 'user',
					'controller' => 'index',
					'action' => 'index',
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
	
		$route = $router->searchRoute('/user/1/index.html');
	
		$this->Route = new Route(
			'/user/:id/:controller/:action',
			array(
				'module' => 'user',
				'controller' => 'index',
				'action' => 'index',
				'prefix' => '',
				'format' => 'html'
			)
		);
	
		$this->Route->setRouter($router);
		$reg = Registry::getInstance()->getRequest();
		$this->assertEquals(
			'http://'.$reg->getHost().'/user/1/test/test.html',
			$this->Route->url(
				array(
					'controller' => 'test',
					'action' => 'test'
				),
				false,
				true
			)
		);
	
		$this->assertEquals(
			'http://lifemeter.ronet.info/user/1/test/test.html',
			$this->Route->url(
				array(
					'controller' => 'test',
					'action' => 'test',
					'hostname' => 'lifemeter.ronet.info'
				),
				false,
				true
			)
		);
		
		
		$this->Route = new Route(
			'/user/:id/:controller/:action',
			array(
				'module' => 'user',
				'controller' => 'index',
				'action' => 'index',
				'prefix' => '',
				'format' => 'html',
				'hostname' => 'lifemeter.ronet.info'
			)
		);
		
		$this->Route->setRouter($router);
		
		$this->assertEquals(
			'http://lifemeter.ronet.info/user/1/test/test.html',
			$this->Route->url(
				array(
					'controller' => 'test',
					'action' => 'test'
				),
				false,
				true
			)
		);
		
		$_SERVER['HTTP_HOST'] = 'lifemeter.ronet.info';
		$this->Route = new Route(
			'/user/:id/:controller/:action',
			array(
				'module' => 'user',
				'controller' => 'index',
				'action' => 'index',
				'prefix' => '',
				'format' => 'html'
			)
		);
		
		$this->Route->setRouter($router);
		
		$this->assertEquals(
			'http://lifemeter.ronet.info/user/1/test/test.html',
			$this->Route->url(
				array(
					'controller' => 'test',
					'action' => 'test'
				),
				false,
				true
			)
		);
		unset($_SERVER['HTTP_HOST']);
		
		
		define('DOMAIN', 'lifemeter.ronet.info');
		
		$this->Route = new Route(
			'/user/:id/:controller/:action',
			array(
				'module' => 'user',
				'controller' => 'index',
				'action' => 'index',
				'prefix' => '',
				'format' => 'html'
			)
		);
		
		$this->Route->setRouter($router);
		
		$this->assertEquals(
			'http://lifemeter.ronet.info/user/1/test/test.html',
			$this->Route->url(
				array(
					'controller' => 'test',
					'action' => 'test'
				),
				false,
				true
			)
		);
	}
	
	/**
	 * Tests Route->getParam(), Route->getParams
	 *
	 * @return void
	 */
	public function testGetParam()
	{
		$routes = array(
			array(
				'key' => 'user',
				'path' => '/user/:id/:controller/:action',
				'defaults' => array(
					'module' => 'user',
					'controller' => 'index',
					'action' => 'index',
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
	
		$route = $router->searchRoute('/user/1/index.html');
	
		$this->Route = new Route(
			'/user/:id/:controller/:action',
			array(
				'module' => 'user',
				'controller' => 'index',
				'action' => 'index',
				'prefix' => '',
				'format' => 'html'
			)
		);
	
		$this->Route->setRouter($router);
	
		$this->assertEquals(1, $this->Route->get('id'));
		$this->assertTrue(is_array($this->Route->getParams()));
	}
	
}

