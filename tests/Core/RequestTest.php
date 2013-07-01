<?php
/**
 * Unittest für Core\Request
 *
 * PHP version 5.3
 *
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace tests\Core;

use Core\Request;

/**
 * Request test case.
 * 
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Beinhaltet eine Instanz von Request
	 * 
	 * @var Request
	 */
	private $Request;
	
	/**
	 * Prepares the environment before running a test.
	 * 
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		$this->Request = new Request(array(), array(), array(), array());
	}
	
	/**
	 * Cleans up the environment after running a test.
	 * 
	 * @return void
	 */
	protected function tearDown()
	{
		$this->Request = null;
		parent::tearDown();
	}
	
	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
	}
	
	/**
	 * Tests Request->__construct()
	 * 
	 * @covers Core\Request::__construct
	 * 
	 * @expectedException \PHPUnit_Framework_Error
	 * 
	 * @return void
	 */
	public function testConstruct()
	{
		$this->Request = new Request();
	
	}
	
	/**
	 * Tests Request->isAjax()
	 * 
	 * @return void
	 */
	public function testIsAjax()
	{
		$this->assertFalse($this->Request->isAjax());
	
		$_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
		$this->Request = new Request(array(), array(),$_SERVER, array());
		$this->assertTrue($this->Request->isAjax());
	}
	
	/**
	 * Tests Request->isHTTPS()
	 * 
	 * @return void
	 */
	public function testIsHTTPS()
	{
		$this->assertFalse($this->Request->isHTTPS());
	
		$_SERVER['HTTPS'] = '1';
		$this->Request = new Request(array(), array(),$_SERVER, array());
		$this->assertTrue($this->Request->isHTTPS());
	
		unset($_SERVER['HTTPS']);
		$_SERVER['HTTP_X_CLIENT_VERIFY'] = 'SUCCESS';
		$this->Request = new Request(array(), array(),$_SERVER, array());
		$this->assertTrue($this->Request->isHTTPS());
		
	}
	
	/**
	 * Tests Request::isMobile()
	 * 
	 * @return void
	 */
	public function testIsMobile()
	{
		$_SERVER['HTTP_USER_AGENT'] = 'FireFox';
		$this->Request = new Request(array(), array(),$_SERVER, array());
		$this->assertFalse($this->Request->isMobile());
	
		$_SERVER['HTTP_USER_AGENT'] = 'iphone';
		$this->Request = new Request(array(), array(),$_SERVER, array());
		$this->assertTrue($this->Request->isMobile());
	}
	
	/**
	 * Tests Request->getMethod()
	 * 
	 * @return void
	 */
	public function testGetMethod()
	{
		$this->assertNull($this->Request->getMethod());
	
	}
	
	/**
	 * Tests Request->isGet()
	 * 
	 * @return void
	 */
	public function testIsGet()
	{
		$this->assertFalse($this->Request->isGet());
	
	}
	
	/**
	 * Tests Request->isPost()
	 * 
	 * @return void
	 */
	public function testIsPost()
	{
		$this->assertFalse($this->Request->isPost());
	
	}
	
	/**
	 * Tests Request->getHost()
	 * 
	 * @return void
	 */
	public function testGetHost()
	{
		// $this->assertEquals('sioux', $this->Request->getHost());
	
		$_SERVER['HTTP_HOST'] = 'localhost';
		$this->Request = new Request(array(), array(),$_SERVER, array());
		$this->assertEquals('localhost', $this->Request->getHost());
		unset($_SERVER['HTTP_HOST']);
	}

	/**
	 * Tests Request->getClientIp()
	 * 
	 * @return void
	 */
	public function testGetClientIp()
	{
		$this->assertFalse($this->Request->getClientIp());
	
		$_SERVER['HTTP_X_FORWARDED_FOR'] = '127.0.0.1';
		$this->Request = new Request(array(), array(),$_SERVER, array());
		$this->assertEquals('127.0.0.1', $this->Request->getClientIp());
		unset($_SERVER['HTTP_X_FORWARDED_FOR']);
		
		$_SERVER['HTTP_X_REAL_IP'] = '127.0.0.1';
		$this->Request = new Request(array(), array(),$_SERVER, array());
		$this->assertEquals('127.0.0.1', $this->Request->getClientIp());
		unset($_SERVER['HTTP_X_REAL_IP']);
		
		$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
		$this->Request = new Request(array(), array(),$_SERVER, array());
		$this->assertEquals('127.0.0.1', $this->Request->getClientIp());
		unset($_SERVER['REMOTE_ADDR']);
		
	}
	
	/**
	 * Tests Request->setParams()
	 * 
	 * @return void
	 */
	public function testSetParams()
	{
		$this->Request->setParams(array());
		$this->assertTrue(is_array($this->Request->getParams()));
		$this->assertEmpty($this->Request->getParams());
		
	}
	
	/**
	 * Tests Request->getParam()
	 * 
	 * @return void
	 */
	public function testGetParam()
	{
		$this->Request->setParams(array('test' => 'value'));
		$this->assertEquals('value', $this->Request->getParam('test'));
	}
	
	/**
	 * Tests Request->get()
	 * 
	 * @return void
	 */
	public function testGet()
	{
		$this->assertEmpty($this->Request->getParamIfExist('test'));
		$this->Request->setParameter('test', 'value');
		$this->assertEquals('value', $this->Request->getParamIfExist('test'));

//		$_GET['test'] = array('test','testb');
		$this->Request->setParameter('test', array('test','testb'));
		$this->assertTrue(is_array($this->Request->getParamIfExist('test')));
	}
	
	/**
	 * Tests Request->post()
	 * 
	 * @return void
	 */
	public function testPost()
	{
		$this->assertEmpty($this->Request->getPostIfExist('test'));
		$this->Request->setPost('test', 'value');
		$this->assertEquals('value', $this->Request->getPostIfExist('test'));
		
		$this->Request->setPost('test', array('test', 'testb'));
		$this->assertTrue(is_array($this->Request->getPostIfExist('test')));
	
	}
	
	/**
	 * Tests Request->request()
	 * 
	 * @return void
	 */
	public function testRequest()
	{
		$this->assertEmpty($this->Request->request('test'));
		$this->Request->setPost('test', 'value');
		$this->assertEquals('value', $this->Request->request('test'));
		$this->Request->unsetPost('test');

		$this->Request->setParameter('test', array('test','testb'));
		$this->assertTrue(is_array($this->Request->request('test')));
	}
	
	/**
	 * Tests Request->files()
	 * 
	 * @return void
	 */
	public function testFiles()
	{
		// TODO Auto-generated RequestTest->testFiles()
		$this->markTestSkipped("files test not implemented");
		
	
	}

}

