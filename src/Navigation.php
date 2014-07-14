<?php

namespace Core;

/**
 * Class Navigation
 *
 * @category Core
 * @package  Core
 * @author   Martin Eisenführer <amrtin@dreiwerken.de>
 */
class Navigation
{

	protected $files = array();
	protected $links = array();
	protected $hasActions = array();
	protected $controllerTitles = array();
	protected $moduleTitles = array();

	/**
	 * Konstruktor
	 */
	public function __construct()
	{
		$this->open(SITE_PATH);
		$this->extract();
	}

	/**
	 * Rendert die Navigation
	 *
	 * @param null $user
	 * @return string
	 */
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

		$navigation = '<div class="tabmenu navigation">';
		$navigation .= '<ul>';

		foreach ($groups as $group => $actions)
		{
			if (empty($actions['links']))
			{
				continue;
			}
			$point = '';

			$links = $actions['links'];
			$linksCount = count($links);
			ksort($links);
			$keys = array_keys($links);

			$first = array_shift($keys);

			$point .= '<li class="{current}"><a href="' . $links[$first]['url'] . '" class="' . $actions['class'] . '"><span>' . $group . '</span></a>';

			if ($linksCount > 1)
			{
				$point .= '<ul class="subnav">';
			}

			$subPoints = '';

			$sp = array();

			$counter = count($links);

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

					$class = '\Core\Application\Manager\Right';
					if (class_exists('\App\Manager\Right'))
					{
						$class = '\App\Manager\Right';
					}

					if ($class::isAllowed($right, $user))
					{
						$sp[ucfirst($action['module']) . '_' . ucfirst($action['controller'])][] = '<li><a href="' . $action['url'] . '"><span>' . $action['title'] . '</span></a></li>';
					}

					$link = strtolower($action['prefix'] . $action['module'] . $action['controller'] . $action['action'] . 'html');

					if ($link == $current)
					{
						$point = str_replace('{current}', 'current', $point);
					}
				}
				else
				{
					$sp[ucfirst($action['module']) . '_' . ucfirst($action['controller'])][] = '<li><a href="' . $action['url'] . '"><span>' . $action['title'] . '</span></a></li>';

					$link = strtolower($action['prefix'] . $action['module'] . $action['controller'] . $action['action'] . 'html');

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
					if (count($actions) == 1)
					{
						$subPoints .= $actions[0];
					}
					else
					{
						$exp = explode('_', $controller);

						if (isset($this->controllerTitles[$exp[0]][$exp[1]]))
						{
							$controller = $this->controllerTitles[$exp[0]][$exp[1]];
						}

						$subPoints .= '<li><a href="#"><span>' . $controller . ' &raquo;</span></a>';
						$subPoints .= '<ul class="subnav">';
						$subPoints .= implode('', $actions);
						$subPoints .= '</ul>';
						$subPoints .= '</li>';
					}
				}
			}

			if ($linksCount > 1)
			{
				$point .= $subPoints;
				$point .= '</ul>';
			}

			$point = str_replace('{current}', '', $point);
			$point .= '</li>';
			if (!empty($subPoints))
			{
				$navigation .= $point;
			}
		}

		$navigation .= '</ul>';
		$navigation .= '</div>';

		return $navigation;
	}

	/**
	 * @param string $dir
	 * @return void
	 */
	private function open($dir)
	{
		$temp = explode('/', $dir);

		if (array_pop($temp) == 'Views')
		{
			return;
		}

		$directory = opendir($dir);

		while (($file = readdir($directory)) == true)
		{
			if ($file != '.' && $file != '..')
			{
				if (is_dir($dir . '/' . $file))
				{
					$this->open($dir . '/' . $file);
				}
				else
				{
					if (\Core\String::endsWith($file, '.php'))
					{
						$this->files[] = $dir . '/' . $file;
					}
				}
			}
		}
	}

	/**
	 * @return void
	 */
	private function extract()
	{
		$view = new \Core\View();

		foreach ($this->files as $controller)
		{
			// Hole Modul und Controllername aus dem Dateinamen heraus
			preg_match("/.*\/Modules(\/[A-Z]{1}[a-zA-Z]+)*\/([A-Z]{1}[a-zA-Z]+)\/Controller\/([A-Z]{1}[a-zA-Z]+)\.php/", $controller, $matches);

			if (!empty($matches) && (count($matches) == 3 || count($matches) == 4))
			{
				$prefix = substr($matches[1], 1);
				if (!$prefix)
				{
					$prefix = '';
				}
				$module = $matches[2];
				$controller = $matches[3];

				if ($prefix != '')
				{
					$class = "\\App\\Modules\\" . ucfirst($prefix) . "\\" . ucfirst($module) . "\\Controller\\" . ucfirst($controller);
				}
				else
				{
					$class = "\\App\\Modules\\" . ucfirst($module) . "\\Controller\\" . ucfirst($controller);
				}

				// Neue Reflectionklasse instanziieren
				$reflect = new \ReflectionClass($class);
				// Methoden auslesen
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
				if ($classDoc !== false)
				{
					preg_match('/.*\@title([A-Za-z0-9äöüÄÖÜ \-\s\t]+).*/s', $classDoc, $matchClassDoc);
					if (!empty($matchClassDoc))
					{
						$this->controllerTitles[$module][$controller] = trim($matchClassDoc[1]);
					}
					preg_match('/.*\@modulTitle([A-Za-z0-9äöüÄÖÜ \-\s\t]+).*/s', $classDoc, $matchClassDoc);
					if (!empty($matchClassDoc) && !isset($this->moduleTitles[$module]))
					{
						$this->moduleTitles[$module] = trim($matchClassDoc[1]);
					}
				}

				foreach ($methods as $method)
				{
					// Prüfe ob eine Methode eine HTML-Action ist
					preg_match("/(.+)(HTML|Html|JSON|Json)Action/", $method->getName(), $matches);
					if (!empty($matches))
					{
						// Lade den Kommentar
						$docComment = $method->getDocComment();

						if ($docComment !== false)
						{
							// Prüfe, ob im Kommentare der Tag showInNavigation vorhanden ist und ob der Wert dann auch true ist
							preg_match('/.*\@showInNavigation([a-z\s\t]+).*/', $docComment, $matchDoc);

							if (!empty($matchDoc) && trim($matchDoc[1]) == 'true')
							{

								if (\jamwork\common\Registry::getInstance()->hasEventDispatcher())
								{
									$eventDispatcher = \jamwork\common\Registry::getInstance()->getEventDispatcher();
									$event = $eventDispatcher->triggerEvent('onAddNavigation', $docComment, $method->getName());
									if ($event->isCanceled())
									{
										continue;
									}
								}


								// Name des Navigationspunktes ermitteln
								//preg_match('/.*\@navigationName([A-Za-z0-9äöüÄÖÜ -\/\t]+).*$/s', $docComment, $matchDoc);
								//$navigationName = trim($matchDoc[1]);
								$navigationName = $this->analyzeDocComment($docComment, '/.*\@navigationName([A-Za-z0-9äöüÄÖÜ -\/\t]+).*$/s');

								//Sortierung des Navigationspunktes ermitteln
								//preg_match('/.*\@navigationSort([0-9 \t]+).*/s', $docComment, $matchDoc);
								//$navigationSort = trim($matchDoc[1]);
								$navigationSort = $this->analyzeDocComment($docComment, '/.*\@navigationSort([0-9 \t]+).*/s');

								//Gruppierung des Navigationspunktes ermitteln
								//preg_match('/.*\@navigationGroup([A-Za-z0-9äöüÄÖÜ \t]+).*/s', $docComment, $matchDoc);
								//$navigationGroup = trim($matchDoc[1]);
								$navigationGroup = $this->analyzeDocComment($docComment, '/.*\@navigationGroup([A-Za-z0-9äöüÄÖÜ \t]+).*/s');


								/*
								 * Config für Navigationspunkt definieren
								 *
								 * Module, Controller und Action werden für die Berechtigungen benötigt
								 */
								$conf = array(
									'module' => $this->convertToPath($module),
									'controller' => $this->convertToPath($controller),
									'action' => $this->convertToPath($matches[1]),
									'format' => strtolower($matches[2]),
								);

								$conf['url'] = $view->url($conf, 'default');
								$conf['prefix'] = $prefix;
								$conf['title'] = $navigationName;
								$conf['permissions'] = $checkPermission;

								$this->links[$navigationGroup][$navigationSort . '-' . $navigationName] = $conf;
								$this->hasActions[$conf['url']] = $conf;
							}
						}
					}
				}
			}
		}

	}

	protected function analyzeDocComment($docComment, $pattern){
		preg_match($pattern, $docComment, $matchDoc);
		return trim($matchDoc[1]);
	}

	/**
	 * Ausgelagert, damit es überschrieben werden kann
	 *
	 * @param string $str
	 * @return string
	 */
	protected function convertToPath($str)
	{
		return strtolower($str);
	}
}
