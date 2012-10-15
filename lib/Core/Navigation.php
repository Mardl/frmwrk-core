<?php
/**
 * Core\Cli-Class
 *
 * PHP version 5.3
 *
 * @category Helper
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace Core;

class Navigation
{
	protected $files = array();
	protected $links = array();
	protected $controllerTitles = array();

	public function __construct(){
		$this->_open(SITE_PATH);
		$this->_extract();
	}

	public function render($user = null)
	{
		$groups = \jamwork\common\Registry::getInstance()->conf->NAVGROUPS;

		$current = strtolower(\jamwork\common\Registry::getInstance()->getRequest()->getParam('prefix'));
		$current .= strtolower(\jamwork\common\Registry::getInstance()->getRequest()->getParam('module'));
		$current .= strtolower(\jamwork\common\Registry::getInstance()->getRequest()->getParam('controller'));
		$current .= strtolower(\jamwork\common\Registry::getInstance()->getRequest()->getParam('action'));
		$current .= strtolower(\jamwork\common\Registry::getInstance()->getRequest()->getParam('format'));

		if (!empty($groups))
		{
			foreach ($groups as $name => $arr)
			{
				if (isset($this->links[$name]))
				{
					$groups[$name]['links'] = $this->links[$name];
					unset($this->links[$name]);
				}
			}

			if (count($this->links) > 0)
			{
				foreach ($this->links as $group => $actions)
				{
					$groups[$group]['class'] = '';
					$groups[$group]['links'] = $actions;
				}
			}
		}
		else
		{
			$groups = array();
			foreach ($this->links as $group => $actions)
			{
				$groups[$group]['class'] = '';
				$groups[$group]['links'] = $actions;
			}
		}
		$navigation = '<div class="tabmenu">';
		$navigation .= '<ul>';


		foreach ($groups as $group => $actions)
		{
			if (empty($actions['links'])){
				continue;
			}
			$point = '';

			$links = $actions['links'];
			ksort($links);
			$keys = array_keys($links);

			$first = array_shift($keys);

			$point .= '<li class="{current}"><a href="'.$links[$first]['url'].'" class="'.$actions['class'].'"><span>'.$group.'</span></a>';

			$point .= '<ul class="subnav">';

			$subPoints = '';

			$sp = array();
			foreach ($links as $action)
			{
				if ($action['permissions'] && class_exists('\App\Models\Right'))
				{
					$data = array(
						'module' => lcfirst($action['module']),
						'controller' => lcfirst($action['controller']),
						'action' => lcfirst($action['action']),
						'prefix' => lcfirst($action['prefix'])
					);

					$right = new \App\Models\Right($data);

					if (class_exists('\App\Manager\Right'))
					{
						if (\App\Manager\Right::isAllowed($right, $user)){
							$sp[ucfirst($action['module']).'_'.ucfirst($action['controller'])][] = '<li><a href="'.$action['url'].'"><span>'.$action['title'].'</span></a>';
						}
					}
					else
					{
						if (\Core\Application\Manager\Right::isAllowed($right, $user)){
							$sp[ucfirst($action['module']).'_'.ucfirst($action['controller'])][] = '<li><a href="'.$action['url'].'"><span>'.$action['title'].'</span></a>';
						}
					}

					$link = strtolower($action['prefix'].$action['module'].$action['controller'].$action['action'].'html');

					if ($link == $current)
					{
						$point = str_replace('{current}', 'current', $point);
					}
				}
				else
				{
					$sp[ucfirst($action['module']).'_'.ucfirst($action['controller'])][] = '<li><a href="'.$action['url'].'"><span>'.$action['title'].'</span></a>';

					$link = strtolower($action['prefix'].$action['module'].$action['controller'].$action['action'].'html');

					if ($link == $current)
					{
						$point = str_replace('{current}', 'current', $point);
					}
				}


			}

			if (count($sp) == 1)
			{
				$key = array_keys($sp);
				$subPoints .= implode('', $sp[$key[0]]);
			}
			else
			{
				foreach ($sp as $controller => $actions)
				{
					if (count($actions) == 1){
						$subPoints .= $actions[0];
					} else {
						$exp = explode('_',$controller);

						if (isset($this->controllerTitles[$exp[0]][$exp[1]]))
						{
							$controller = $this->controllerTitles[$exp[0]][$exp[1]];
						}

						$subPoints .= '<li><a href="#"><span>'.$controller.' &raquo;</span></a>';
						$subPoints .= '<ul class="subnav">';
						$subPoints .= implode('', $actions);
						$subPoints .= '</ul>';
						$subPoints .= '</li>';
					}
				}
			}

			$point .= $subPoints;

			$point .= '</ul>';

			$point = str_replace('{current}', '', $point);

			if (!empty($subPoints))
			{
				$navigation .= $point;
			}



		}

		$navigation .= '</ul>';
		$navigation .= '</div>';

		return $navigation;
	}

	private function _open($dir)
	{
		$temp = explode('/', $dir);
		if ( array_pop($temp) == 'Views'){
			return;
		}
		$directory = opendir($dir);
		while ( ($file = readdir($directory)) == true )
		{
			if ($file != '.' && $file != '..')
			{
				if (is_dir($dir.'/'.$file))
				{
					$this->_open($dir.'/'.$file);
				}
				else
				{
					$this->files[] = $dir.'/'.$file;
				}

			}
		}
	}

	private function _extract()
	{
		$view = new \Core\View();

		foreach ($this->files as $controller)
		{
			//Hole Modul und Controllername aus dem Dateinamen heraus
			preg_match("/.*\/Modules(\/[A-Z]{1}[a-zA-Z]+)*\/([A-Z]{1}[a-zA-Z]+)\/Controller\/([A-Z]{1}[a-zA-Z]+)\.php/",$controller, $matches);

			if (!empty($matches) && (count($matches) == 3 || count($matches) == 4))
			{
				$prefix = substr($matches[1],1);
				$module = $matches[2];
				$controller = $matches[3];

				if ($prefix != '')
				{
					$class = "\\App\\Modules\\".$prefix."\\".$module."\\Controller\\".$controller;
				}
				else
				{
					$class = "\\App\\Modules\\".$module."\\Controller\\".$controller;
				}

				//Neue Reflectionklasse instanzieren
				$reflect = new \ReflectionClass($class);
				//Methoden auslesen
				$methods = $reflect->getMethods();
				$properties = $reflect->getDefaultProperties();

				if (isset($properties['checkPermissions']))
				{
					$checkPermission = $properties['checkPermissions'];
				}
				else
				{
					$checkPermission = CHECK_PERMISSIONS;
				}

				$classDoc = $reflect->getDocComment();
				if ($classDoc !== false){
					preg_match('/.*\@title ([A-Za-z0-9äöüÄÖÜ]+).*/s', $classDoc, $matchClassDoc);
					if (!empty($matchClassDoc)){
						$this->controllerTitles[$module][$controller] = $matchClassDoc[1];
					}
				}

				foreach ($methods as $method)
				{
					//Prüfe ob eine Methode eine HTML-Action ist
					preg_match("/(.+)(HTML|Html)Action/", $method->getName(), $matches);
					if (!empty($matches)){
						//Lade den Kommentar
						$docComment = $method->getDocComment();

						if ($docComment !== false)
						{
							//Prüfe ob im Kommentare der Tag showInNavigation vorhanden is und ob der Wert dann auch true ist
							preg_match('/.*\@showInNavigation ([a-z]+).*/', $docComment, $matchDoc);

							if (!empty($matchDoc) && $matchDoc[1] == 'true'){
								//Name des Navigationspunktes ermitteln
								preg_match('/.*\@navigationName ([A-Za-z0-9äöüÄÖÜ -\/]+).*$/s', $docComment, $matchDoc);
								$navigationName = $matchDoc[1];

								//Sortierung des Navigationspunktes ermitteln
								preg_match('/.*\@navigationSort ([0-9]+).*/s', $docComment, $matchDoc);
								$navigationSort = $matchDoc[1];

								//Gruppierung des Navigationspunktes ermitteln
								preg_match('/.*\@navigationGroup ([A-Za-z0-9äöüÄÖÜ]+).*/s', $docComment, $matchDoc);
								$navigationGroup = $matchDoc[1];

								/*
								 * Config für Navigationspunkt definieren
								 *
								 * Module, Controller und Action werden für die Berechtigungen benötigt
								 */
								$conf = array(
									'module' => strtolower($module),
									'controller' => strtolower($controller),
									'action' => strtolower($matches[1]),
								);

								$conf['url'] = $view->url($conf, 'default');
								$conf['prefix'] = $prefix;
								$conf['title'] = $navigationName;
								$conf['permissions'] = $checkPermission;

								$this->links[$navigationGroup][$navigationSort.'-'.$navigationName] = $conf;
							}
						}
					}

				}

			}



		}


	}


}
?>