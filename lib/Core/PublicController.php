<?php
/**
 * Core\PublicController-Class
 *
 * PHP version 5.3
 *
 * @category Controller
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */

namespace Core;

use App\Models\Right,
	App\Manager\Right as RightManager,
	jamwork\common\Registry;

/**
 * PublicController Class
 * PublicController inkl. Rechteabfrage
 *
 * @category Controller
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class PublicController extends Controller
{
	protected $checkPermissions = true;

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();

		$module 	= $this->request->getRouteParam('module');
		$controller = $this->request->getRouteParam('controller');
		$action		= $this->request->getRouteParam('action');
		$prefix		= $this->request->getRouteParam('prefix');

		$right = new Right(
			array(
				'module' => $module,
				'controller' => $controller,
				'action' => $action,
				'prefix' => $prefix
			)
		);

		if ($this->checkPermissions)
		{
			try {
				$login = Registry::getInstance()->login;
			}
			catch (\Exception $e)
			{
				$this->response->redirect($this->view->url(array(), 'login', true));
			}

			if (!RightManager::isAllowed($right, $login))
			{
				throw new \Exception('Zugriff auf nicht erlaubte Aktion');
			}
		}

		if ($this->view->login)
		{
			$this->view->html->addJsAsset('loggedin');
		}

	}

	/**
	 *
	 * Übergebene Array wird json encodiert und ausgegeben
	 * Header wird sauber angepasst
	 *
	 * @param array $json
	 */
	protected function flushJSON(array $json)
	{
		$registry = Registry::getInstance();
		$response = $registry->getResponse();
		$response->addHeader('Content-Type', 'application/json; charset=utf-8');

		$response->setBody( json_encode($json) );
		$response->flush();
		die();
	}


}
