<?php

namespace Core;

use InvalidArgumentException, Core\Request, Core\FrontController, jamwork\common\Registry;

/**
 * Abstract controller class
 *
 * @category Core
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
abstract class Controller
{

	/**
	 * FrontController
	 *
	 * @var FrontController
	 */
	protected $frontController;

	/**
	 * View
	 *
	 * @var   \Core\View
	 */
	protected $view;

	/**
	 * Request
	 * HTTP request
	 *
	 * @var   Request
	 */
	protected $request;

	/**
	 * Response
	 * HTTP response
	 *
	 * @var   Response
	 */
	protected $response;

	/**
	 * Router
	 *
	 * @var   Router
	 */
	protected $router;

	/**
	 * Holds the rendering state
	 *
	 * @var bool
	 */
	protected $disableRendering = false;

	/**
	 * List of view helpers
	 *
	 * @var   array Helper
	 */
	protected $helpers = array();

	/**
	 * Construct
	 */
	public function __construct()
	{
		$reg = Registry::getInstance();
		$this->request = $reg->getRequest();
		$this->response = $reg->getResponse();
		$this->view = $reg->view;
	}

	/**
	 * Magische Funktion für den Aufruf einer Action
	 *
	 * @param string $method    Name der Action
	 * @param array  $arguments Array mit Argumenten
	 *
	 * @throws \InvalidArgumentException Wenn die Action in dem Controller unbekannt ist
	 *
	 * @return void
	 */
	public function __call($method, $arguments = array())
	{
		$msg = sprintf('Action "%s" not found in controller "%s"', $method, __CLASS__);
		throw new \InvalidArgumentException($msg, 404);
	}

	/**
	 * Speichert den FrontController zwischen
	 *
	 * @param \Core\FrontController $frontController FrontController
	 *
	 * @return void
	 */
	public function setFrontController(FrontController $frontController)
	{
		$this->frontController = $frontController;
	}

	/**
	 * Set router
	 *
	 * @param \Core\Router $router
	 * @return bool Status
	 */
	public function setRouter(Router $router)
	{
		if ($this->view)
		{
			$this->view->setRouter($router);
		}
		$this->router = $router;

		return true;
	}

	/**
	 * Set view
	 *
	 * @param View $view
	 * @return bool
	 */
	public function setView(View $view)
	{
		if ($this->router)
		{
			$view->setRouter($this->router);
		}
		$this->view = $view;

		return true;
	}

	/**
	 * Create new view and set router
	 *
	 * @param string $template Template file
	 *
	 * @return \Core\View
	 */
	public function createView($template = null)
	{
		$view = new View($template);
		$view->setRouter($this->router);

		return $view;
	}

	/**
	 * Set request
	 *
	 * @param \Core\Request $request Request
	 *
	 * @return bool Status
	 */
	public function setRequest(Request $request)
	{
		$this->request = $request;

		return true;
	}

	/**
	 * Set response
	 *
	 * @param \Core\Response $response Response
	 *
	 * @return bool Status
	 */
	public function setResponse(Response $response)
	{
		$this->response = $response;

		return true;
	}

	/**
	 * Disables the rendering for the controller
	 *
	 * @param bool $flag Entweder True oder False
	 *
	 * @return void
	 */
	public function setNoRender($flag = true)
	{
		$this->disableRendering = $flag;
	}

	/**
	 * Returns the render state for the view
	 *
	 * @return bool
	 */
	public function isNoRender()
	{
		return $this->disableRendering;
	}

	/**
	 * Fügt die Seiteninfos dem Frontcontroller hinzu
	 *
	 * @param string $action     Actionname
	 * @param string $controller Controllername
	 * @param string $module     Modulname
	 * @param string $format     Format
	 *
	 * @return void
	 */
	public function forward($action, $controller = null, $module = null, $format = null)
	{
		$this->frontController->addPageToStack($action, $controller, $module, $format);
	}

	/**
	 * Template method which can be overwritten to init the controller
	 *
	 * @return void
	 */
	public function init()
	{

	}
}
