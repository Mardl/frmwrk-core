<?php

namespace Core;

use jamwork\common\Registry, Core\SystemMessages;

/**
 * Class HTMLHelper
 *
 * @category Core
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class HTMLHelper
{

	/**
	 * Meta-Angaben
	 *
	 * @var array
	 */
	private $_metas = array();

	/**
	 * CSS files
	 *
	 * @var array CSS-Dateien
	 */
	private $_cssFiles = array();

	/**
	 * Javascript assets
	 *
	 * @var array
	 */
	private $_jsAssets = array();

	/**
	 * Javascript variables
	 *
	 * @var array
	 */
	private $_jsVariables = array();

	/**
	 * Javascript files
	 *
	 * @var array JS-Files.
	 */
	private $_jsFiles = array();

	/**
	 * Breadcrumbs
	 *
	 * @var array
	 */
	private $_breadcrumbs = array();

	/**
	 * Breadcrumb url for home (little house)
	 *
	 * @var string
	 */
	private $_breadcrumbHome;

	/**
	 * Verlinkungen auf JS,CSS
	 *
	 * @var array
	 */
	private $_links = array();

	protected $parentView;

	/**
	 * Constructor
	 *
	 * @param View $view
	 */
	public function __construct(\Core\View $view = null)
	{
		$this->parentView = $view;
	}

	/**
	 * Get url to app
	 *
	 * @param string $path Path
	 *
	 * @return string
	 */
	public function app($path)
	{
		return (defined('APP_URL') ? APP_URL . $path : $path);
	}

	/**
	 * Get system messages as HTML
	 *
	 * @param string $alternativ
	 * @return string
	 */
	public function getSystemMessages($alternativ = '')
	{
		$messages = SystemMessages::getList();
		if (count($messages) == 0)
		{
			return '';
		}
		if (!empty($alternativ))
		{
			$view = new View(APPLICATION_PATH . $alternativ);
		}
		else
		{
			$view = new View(APPLICATION_PATH . '/Layout/Helpers/systemmessages.html.php');
		}
		$view->messages = $messages;
		SystemMessages::clear();

		return $view->render();
	}

	/**
	 * Set css file and remove previous
	 *
	 * @param string $filename   Pfad zur CSS-Datei
	 * @param array  $attributes Zusätzliche Attribute
	 *
	 * @return void
	 */
	public function setCSSFile($filename, $attributes = array())
	{
		$this->_cssFiles = array();
		$this->addCSSFile($filename, $attributes);
	}

	/**
	 * Add css assent
	 *
	 * @param string $name Name of asset defined in config
	 *
	 * @return void
	 */
	public function addCssAsset($name)
	{
		if (isset(Registry::getInstance()->conf) && isset(Registry::getInstance()->conf->CSS_ASSETS[$name]))
		{
			foreach (Registry::getInstance()->conf->CSS_ASSETS[$name] as $cssFile)
			{
				if (!in_array($cssFile, $this->_cssFiles))
				{
					$this->addCSSFile($cssFile);
				}
			}
		}
	}

	/**
	 * Add css file
	 *
	 * @param string $filename   Pfad zur CSS-Datei
	 * @param array  $attributes Zusätzliche Attribute
	 *
	 * @return void
	 */
	public function addCSSFile($filename, $attributes = array())
	{
		$attributes += array(
			'type' => 'text/css',
			'rel' => 'stylesheet'
		);

		if (!\Core\String::startsWith($filename, 'http'))
		{
			$attributes['href'] = $this->app('css/' . $filename);
		}
		else
		{
			$attributes['href'] = $filename;
		}

		// $attributes['href'] = $this->app('css/'.$filename);
		$this->_cssFiles[] = $attributes;
	}

	/**
	 * Get css files
	 *
	 * @return array
	 */
	public function getCSSFiles()
	{
		return $this->_cssFiles;
	}

	/**
	 * Liefert die Link-Angaben zu CSS-Einbindungen
	 *
	 * @return string
	 */
	public function renderCSSFiles()
	{
		$result = '';
		foreach ($this->getCSSFiles() as $file)
		{
			$link = '<link';
			foreach ($file as $key => $value)
			{
				$link .= ' ' . $key . '="' . $value . '"';
			}
			$link .= ' />';
			$result .= $link;
		}

		return $result;
	}

	/**
	 * Add javascript assent
	 *
	 * @param string $name Name of asset defined in config
	 *
	 * @return void
	 */
	public function addJsAsset($name)
	{
		$this->_jsAssets[] = $name;
	}

	/**
	 * Set javascript asset and remove previous
	 *
	 * @param string $name Name of asset defined in config
	 *
	 * @return void
	 */
	public function setJsAsset($name)
	{
		$this->_jsAssets = array($name);
	}

	/**
	 * Add javascript filename
	 *
	 * @param string $filename Filename
	 *
	 * @return void
	 */
	public function addJsFile($filename)
	{
		$this->_jsFiles[] = $filename;
	}

	/**
	 * Get javascript assets
	 *
	 * @return array
	 */
	public function getJsAssets()
	{
		return $this->_jsAssets;
	}

	/**
	 * Get filenames from javascript assets
	 *
	 * @return array
	 */
	public function getJsAssetFiles()
	{
		$config = Registry::getInstance()->conf;

		$assetFiles = $config->JS_ASSETS['default'];
		foreach ($this->_jsAssets as $name)
		{
			if (array_key_exists($name, $config->JS_ASSETS))
			{
				$assetFiles = array_merge($assetFiles, $config->JS_ASSETS[$name]);
				//throw new \Exception('Javascript asset '.$name.' not found', 404);
			}
		}

		$assetFiles = array_merge($assetFiles, $this->_jsFiles);

		return array_unique($assetFiles);
	}

	/**
	 * Get javascript variables
	 *
	 * @return array
	 */
	public function getJsVariables()
	{
		return $this->_jsVariables;
	}

	/**
	 * Set variable in javascript and remove previous
	 *
	 * @param string $name  Name
	 * @param mixed  $value Value
	 *
	 * @return void
	 */
	public function setJsVariable($name, $value)
	{
		$this->_jsVariables = array($name => $value);
	}

	/**
	 * Add variable in javascript
	 *
	 * @param string $name  Name
	 * @param mixed  $value Value
	 *
	 * @return void
	 */
	public function addJsVariable($name, $value)
	{
		$this->_jsVariables[$name] = $value;
	}

	/**
	 * Add entry to breadcrumbs
	 *
	 * @param string $title Title of breadcrumb entry.
	 * @param string $url   Url of breadcrumb (view->url()).
	 *
	 * @return void
	 */
	public function addBreadcrumb($title, $url = null)
	{
		$this->_breadcrumbs[] = array(
			'title' => $title,
			'url' => $url
		);
	}

	/**
	 * Returns all breadcrumbs.
	 *
	 * @return array
	 */
	public function getBreadcrumbs()
	{
		return $this->_breadcrumbs;
	}

	/**
	 * Set link from breadcrumb home (small house)
	 *
	 * @param string $url Url
	 *
	 * @return void
	 */
	public function setBreadcrumbHome($url)
	{
		$this->_breadcrumbHome = $url;
	}

	/**
	 * Get link from breadcrumb home (small house)
	 *
	 * @return string
	 */
	public function getBreadcrumbHome()
	{
		return $this->_breadcrumbHome;
	}

	/**
	 * Render breadcrumbs
	 *
	 * @return \Core\View
	 */
	public function viewBreadcrumbs()
	{
		$view = new View(APPLICATION_PATH . '/Layout/Helpers/breadcrumbs.html.php');
		$view->home = $this->getBreadcrumbHome();
		$view->breadcrumbs = $this->getBreadcrumbs();

		return $view;
	}

	/**
	 * "Cuts" a string at an given length
	 *
	 * @param string $text       the string to cut
	 * @param int    $length     when to cut the string
	 * @param bool   $breakWords flag to indicate if a word should be preserved
	 * @param string $etc        will be added at the of the line
	 *
	 * @return string
	 */
	public function truncate($text, $length, $breakWords = true, $etc = '...')
	{
		if ($length === 0)
		{
			return '';
		}

		if (mb_strlen($text) <= $length)
		{
			return $text;
		}

		$length -= min($length, mb_strlen($etc));

		if (!$breakWords)
		{
			// Dont break words!
			$text = preg_replace('/\s+\S*$/', '', mb_substr($text, 0, $length + 1));
		}

		return mb_substr($text, 0, $length) . $etc;
	}

	/**
	 * Fügt eine Verlinkung hinzu
	 *
	 * @param string|array $attributes Array mit Attributen
	 *
	 * @return void
	 */
	public function addLink($attributes)
	{
		$this->_links[] = $attributes;
	}

	/**
	 * Liefert die Link-Angaben
	 *
	 * @return string
	 */
	public function getRenderedLinks()
	{
		return array_map(
			function($link)
			{
				$result = '<link';
				foreach ($link as $key => $value)
				{
					$result .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
				}
				$result .= ' />';

				return $result;
			},
			$this->_links
		);

		// return $this->metas;
	}

	/**
	 * Fügt eine MetaInformation hinzu
	 *
	 * @param string|array $attributes Array mit Meta Informationen
	 *
	 * @return void
	 */
	public function addMeta($attributes)
	{
		$attributes = preg_replace('#\s+#', ' ', $attributes);
		$attributes = array_map('trim', $attributes);
		$this->_metas[] = $attributes;
	}

	/**
	 * Liefert die Meta-Angaben als Array
	 *
	 * @return array
	 */
	public function getMetas()
	{
		return $this->_metas;
	}

	/**
	 * Liefert die Meta-Angaben als String
	 *
	 * @return string
	 */
	public function renderMetas()
	{
		$result = '';
		foreach ($this->getMetas() as $meta)
		{
			$link = '<meta';
			foreach ($meta as $key => $value)
			{
				$link .= ' ' . $key . '="' . $value . '"';
			}
			$link .= ' />';
			$result .= $link;
		}

		return $result;
	}

	/**
	 * Liefert für ein SELECT-Feld die Optionsliste
	 * Die Optionsliste wird mittels dem übergebenen Array zusammengebaut.
	 * Wobei der Index als Wert und der Value als Anzeige verwendet wird.
	 * Mit dem optionalem Parameter $current, wird definiert welcher Index ausgewählt
	 * sein soll.
	 *
	 * @param array $array   Array mit den Optionwerten
	 * @param mixed $current Optionaler Parameter für die Auswahl einer best. Option
	 *
	 * @return string
	 */
	public function options(array $array, $current = null)
	{
		$sOption = '<option value="%s" %s>%s</option>';

		$options = array();

		foreach ($array as $key => $value)
		{
			$options[] = sprintf($sOption, $key, ($current != $key) ? : 'selected="selected"', $value);
		}

		return implode("\n", $options);
	}

	/**
	 * Liefert einen Standardlink verbunden mit Rechteprüfung
	 *
	 * @param string $name            Name des Links
	 * @param array  $data            Daten für den Linkaufbau
	 * @param array  $css             Array mit den CSS-Klassen als Value
	 * @param array  $attributes      Array mit zusätzlichen Linkattributen, Aufbau ATTRIBUTE=>VALUE
	 * @param string $route           Name der zu verwendenden Route
	 * @param string $reset           Überschreiben fehlender Attribute mit den Standardwerten
	 * @param bool   $absolute        http davor setzen oder nicht
	 *
	 * @return string|null
	 */
	public function anchor($name, array $data = array(), $css = array(), $attributes = array(), $route = null, $reset = null, $absolute = false)
	{
		//var_dump($data);
		$url = $this->parentView->url($data, $route, $reset, $absolute);
		//var_dump($url);
		if (is_null($route))
		{
			$route = $this->parentView->getRoute()->matchUrl($url);
		}
		else
		{
			$route = \jamwork\common\Registry::getInstance()->router->offsetGet($route)->matchUrl($url);
		}
		//var_dump($route);

		$link = $url;

		if (substr($link, 0, 4) != 'http' && class_exists('\App\Models\Right'))
		{
			$link = null;

			if ($route['prefix'] == '')
			{
				$controller = '\\App\Modules\\' . ucfirst($route['module']) . '\\Controller\\' . ucfirst($route['controller']);
			}
			else
			{
				$controller = '\\App\Modules\\' . ucfirst($route['prefix']) . '\\' . ucfirst($route['module']) . '\\Controller\\' . ucfirst($route['controller']);
			}

			try
			{
				$reflection = new \ReflectionClass($controller);
			} catch (\Exception $e)
			{
				\Core\SystemMessages::addError($e->getMessage());

				return '';
			}

			$properties = $reflection->getDefaultProperties();

			if ($properties['checkPermissions'] == false)
			{
				$link = $url;
			}

			if (is_null($link))
			{
				$data = array(
					'module' => $route['module'],
					'controller' => $route['controller'],
					'action' => $route['action'],
					'prefix' => $route['prefix']
				);
				$right = new \App\Models\Right($data);

				if (class_exists('\App\Manager\Right'))
				{
					$allowed = \App\Manager\Right::isAllowed($right, Registry::getInstance()->login);
				}
				else
				{
					$allowed = \Core\Application\Manager\Right::isAllowed($right, Registry::getInstance()->login);
				}

				if ($allowed)
				{
					$link = $url;
				}
			}
		}

		if (!is_null($link))
		{
			$anchorString = "<a href='%s'%s%s>%s</a>";

			$classes = '';
			$attr = '';

			if (!empty($css))
			{
				$classes = ' class="';
				$classes .= implode(' ', $css);
				$classes .= '"';
			}

			if (!empty($attributes))
			{
				$attr = ' ';
				foreach ($attributes as $attribt => $value)
				{
					$attr .= $attribt . '="' . $value . '" ';
				}
			}

			return sprintf($anchorString, $link, $classes, $attr, $name);
		}

		return $link;
	}
}
