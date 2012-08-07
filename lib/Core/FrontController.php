<?php
/**
 * Core\Frontcontroller-Class
 *
 * PHP version 5.3
 *
 * @category Controller
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */

namespace Core;

use Exception,
    InvalidArgumentException,
    jamwork\common\Registry,
	Core\HTMLHelper;

/**
 * FrontController
 *
 * Set up view, router, request and response.
 * Dispatch url and execute controller and action by defined routes in router.
 * Render view and return result.
 * 
 * @category Controller
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class FrontController
{

	/**
	 * View
	 *
	 * @var   Core\View
	 */
	protected $view;

	/**
	 * Router
	 *
	 * @var   Core\Router
	 */
	protected $router;

	/**
	 * Request
	 *
	 * @var   Core\Request
	 */
	protected $request;

	/**
	 * Response
	 *
	 * @var   Core\Response
	 */
	protected $response;

	/**
	 * Current page on stack
	 *
	 * @var array
	 */
	protected $currentPage;

	/**
	 * Last page on stack
	 *
	 * @var array
	 */
	protected $lastPage;

	/**
	 * Stack on pages to render
	 *
	 * @var array
	 */
	protected $stack = array();

	/**
	 * Constructor
	 */
	public function __construct() 
	{
		$this->router = Registry::getInstance()->router;

		$this->request = Registry::getInstance()->getRequest();

		$this->response = Registry::getInstance()->getResponse();

		// View
		if (!isset(Registry::getInstance()->view))
		{
			$view = new View();
			$view->html = new HTMLHelper();
			$view->setTemplate(APPLICATION_PATH.'/Layout/layout.html.php');
			$this->view = $view;
			Registry::getInstance()->view = $view;
		}
		else
		{
			$this->view = Registry::getInstance()->view;
		}
		
	}

	/**
	 * Set router
	 *
	 * @param Core\Router $router Router
	 * 
	 * @return void
	 */
	public function setRouter(Router $router) 
	{
		$this->router = Registry::getInstance()->router;
	}

	/**
	 * Fügt die Seiteninfos dem internen Stack hinzu
	 * 
	 * @param string $action     Actionname
	 * @param string $controller Controllername
	 * @param string $module     Modulname
	 * @param string $format     Format
	 * 
	 * @return void
	 */
	public function addPageToStack($action, $controller = null, $module = null, $format = null) 
	{
			
		$page = array_filter(
			array(
				'action'     => $action,
				'controller' => $controller,
				'module'     => $module,
				'format'     => $format
			)
		);

		if ($this->lastPage)
		{
			if (is_array($this->currentPage))
			{
				$page += $this->currentPage;
			}
		}
		$this->stack[] = $this->lastPage = $page;
	}

	/**
	 * Render
	 *
	 * @param string $actionName     Action name
	 * @param string $controllerName Controller name
	 * @param string $moduleName     Module name
	 * @param string $actionFormat   Action format
	 * 
	 * @return string
	 *
	 * @throws Exception
	 */
	public function render($actionName, $controllerName, $moduleName, $actionFormat = 'html') 
	{
		// Because the validation argument is the same, we use an loop - saves us space
		$parts = array(
			'action' => $actionName,
			'controller' => $controllerName,
			'module' => $moduleName,
			'format' => $actionFormat
		);

		$controllerName = sprintf(
			'App\Modules\%s\Controller\%s',
			ucfirst(str_replace('/', '_', $parts['module'])),
			ucfirst($controllerName)
		);
			
		if (!class_exists($controllerName))
		{
			foreach ($parts as $key => $val)
			{
				if (strlen($val) < 2 || strlen($val) > 32)
				{
					$msg = sprintf(
						'%s must be at least 2 chars long and may not exceed 32',
						ucfirst($val)
					);
					throw new \InvalidArgumentException($msg);
				}

				if (preg_match('#[^a-zA-Z0-9_\/-]#', $val))
				{
					$msg = sprintf('%s contains invalid chars', ucfirst($val));
					throw new \InvalidArgumentException($msg);
				}

				$parts[$key] = str_replace('-', '_', $val);
			}

			// Define the controller file
			$controllerFile = sprintf(
				'%s/Modules/%s/Controllers/%s.php',
				APPLICATION_PATH,
				ucfirst($parts['module']),
				ucfirst($parts['controller'])
			);
			
			if (!file_exists($controllerFile))
			{
				throw new \Exception('Controller file '.$controllerFile.' not found', 404);
			}
			
			try {
				require $controllerFile;
			} 
			catch(Exception $e) 
			{
				throw new \Exception('Controller file '.$controllerFile.' not found', 404);
			}

			if (!class_exists($controllerName, false))
			{
				throw new \Exception('Controller class „'.$controllerFile.'“ not found', 404);
			}

			if (!is_subclass_of($controllerName, 'Core\\Controller'))
			{
				throw new Exception(
					'Controller „'.$controllerName.'“ must extend Core\\Controller'
				);
			}
		}

		// Controller allow us to use Action-Method depending on the formatm e.g. indexHTMLAction
		$class = new $controllerName();
		$method = $this->searchAction($class, $parts['action'], $parts['format']);
		$class->setFrontController($this);
		$class->setRouter($this->router);
		$class->init();

		// Action might set a template, so we need to save the state before
		// the method has been called
		$templates = $this->view->getTemplates();
		$class->$method();

		// Render template when currentPage is last page on stack
		if ($this->currentPage == $this->lastPage)
		{
			if ($class->isNoRender())
			{
				return '';
			}

			// When controller change templates, dont add default template
			if ($templates == $this->view->getTemplates())
			{
				$template = sprintf(
					'%s/Modules/%s/Views/%s/%s.%s.php',
					APPLICATION_PATH,
					ucfirst($parts['module']),
					ucfirst($parts['controller']),
					$parts['action'],
					$parts['format']
				);

				$this->view->addTemplate($template);
			}
			return $this->view->render();
		}
		return '';
	}

	/**
	 * Führt den Aufruf aus
	 * 
	 * @return string
	 */
	public function execute($url = null) 
	{
		if (is_null($url))
		{
			$url = $_SERVER['REQUEST_URI'];
		}
		
		$route = $this->router->searchRoute($url);
		$this->request->setParams($route->getParams());
		
		try {
			
			$action = $this->router->getParam('action');
			$controller = $this->router->getParam('controller');
			$format = $this->router->getParam('format');
			
			if ($this->router->getParam('prefix'))
			{ 
				$module = $this->router->getParam('prefix').$this->router->getParam('module');
			}
			else
			{
				$module = $this->router->getParam('module');
			}
			
			$this->addPageToStack(
				$action,
				$controller,
				$module,
				$format
			);
			
			if (!Registry::getInstance()->getSession()->has('user'))
			{
				$this->view->login = null;
			}
			else
			{
				$this->view->login = \App\Manager\User::getUserById(
					Registry::getInstance()->getSession()->get('user')
				);
				Registry::getInstance()->login = $this->view->login;
			}
			

			/*
			if(isset($_SESSION['user'])) {
				$user = User::findOneById($_SESSION['user']);
				
				if($user) {
					if(!$user->getLastOnline()) {
						$user->setLastOnline('now');
					}
					if($user->getLastOnline() < DateTime::factory('now')->sub('PT1M')) {
						$user->setLastOnline('now');
					}
					$user->save();
					Registry::set('login', $user);
					$this->view->login = $user;
				} else {
					if(defined('GUEST_ID')) {
						$user = User::findOneById(GUEST_ID);
						Registry::set('login', $user);
						$this->view->login = $user;
					}	
				}
			} else {
				if(defined('GUEST_ID')) {
					$user = User::findOneById(GUEST_ID);
					Registry::set('login', $user);
					$this->view->login = $user;
				}	
			}*/

			$result = $this->dispatchLoop();
		}
		catch(Exception $e) 
		{
			$this->stack = array();
			$this->addPageToStack('index', 'error', 'index', 'html');
			$this->view->setTemplate(APPLICATION_PATH.'/Layout/layout.html.php');
			SystemMessages::addError($e->getMessage());
			$this->view->exception = $e;
			$result = $this->dispatchLoop();
		}

		return $result;
	}

	/**
	 * Execute page by current route
	 *
	 * @return string
	 */
	public function dispatchLoop() 
	{
		for ($i=0; $i<count($this->stack); $i++) 
		{
			$this->currentPage = $this->stack[$i];
			$result = $this->render(
				$this->currentPage['action'],
				$this->currentPage['controller'],
				$this->currentPage['module'],
				$this->currentPage['format']
			);
		}

		return $result;
	}

	/**
	 * Sucht nach einer Action in dem übergebenen Controller nach unterschiedlichen Mustern
	 * Gesucht wird nach folgenden Action-Namen: (Beispiel: Action = index, Format=html )
	 * getIndexHtmlAction()
	 * postIndexHtmlAction()
	 * getIndexAction()
	 * postIndexAction()
	 * IndexHtmlAction()
	 * IndexAction()
	 * 
	 * @param object $class  Instanz der Controller-Klasse
	 * @param string $action Name der Action
	 * @param string $format Ausgabeformat
	 * 
	 * @throws \InvalidArgumentException Wenn die Action nicht gefunden wurde
	 * 
	 * @return string
	 */
	public function searchAction($class, $action, $format = 'html') 
	{
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		$action = ucfirst($action);
		$format = ucfirst($format);

		$actionName = $method.$action.$format.'Action';
		if (method_exists($class, $actionName))
		{
			return $actionName;
		}

		$actionName = $method.$action.'Action';
		if (method_exists($class, $actionName))
		{
			return $actionName;
		}

		$actionName = $action.$format.'Action';
		if (method_exists($class, $actionName))
		{
			return $actionName;
		}

		$actionName = $action.'Action';
		if (method_exists($class, $actionName))
		{
			return $actionName;
		}

		$msg = sprintf('Action "%s" not found in controller "%s"', $action, get_class($class));
		throw new \InvalidArgumentException($msg, 404);
	}

}
