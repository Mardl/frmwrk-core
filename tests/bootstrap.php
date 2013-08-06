<?php
/**
 * Init-Skript
 *  
 * PHP version 5.3
 *
 * @category Testing 
 * @package  Unittest
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */



use \jamwork\common\Registry;
use \jamwork\common\HttpSession;

/**
 * Init Klasse
 *
 * Mit Hilfe dieser Klasse wird die Testsuite für die Tests vorbereitet
 * 
 * @category Testing 
 * @package  Unittest
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class InitUnittests
{
	/**
	 * Konstruktor.
	 * 
	 * Führt automatisch jede Funktion, die mit "init" beginnt, aus.
	 */
	public function __construct()
	{
		$methods = get_class_methods(__CLASS__);

		foreach ($methods as $method)
		{
			if (substr($method, 0, 4) == 'init')
			{
				$this->$method();
			}
		}
		
	}
	
	/**
	 * Initialisiere globale Konstanten
	 *
	 * @return void
	 */
	public function initGlobalConstants()
	{
		define('ROOT_PATH', '.');
		define('FRAMEWORK_PATH', ROOT_PATH.'/src');
		define('APPLICATION_PATH', ROOT_PATH.'/tests');
		define('TESTS_PATH', ROOT_PATH.'/tests');
		define('VENDOR_PATH', ROOT_PATH.'/vendor');
		define('FRAMEWORKS_PATH', VENDOR_PATH.'/frameworks');
		define('ENV_DEV', 0);
		define('ENV_STAG', 1);
		define('ENV_PROD', 2);
		define('DB_SERVER', 'localhost');
		define('STATUS_ACTIVE', 0);
		define('STATUS_INACTIVE', 1);
		define('STATUS_BLOCKED', 2);
		define('STATUS_DELETED', 3);
		define('STATUS_ALL', 4);
		
		
	}

	/**
	 * Initialisiere global genutzte, eigene Funktionen
	 *
	 * @return void
	 */
	public function initFunctions()
	{
		/**
		 * Vereint zweit Arrays die by Reference übergeben werden
		 *
		 * @param array &$array1 Pointer to Array1
		 * @param array &$array2 Pointer to Array2
		 *
		 * @return void
		 */
		function merging(&$array1, &$array2)
		{
			foreach ($array2 as $key => $val)
			{
				if (is_scalar($val) || !isset($array1[$key]))
				{
					$array1[$key] = $val;
				}
				else if (is_array($val) && !is_array($array1[$key]))
				{
					$array1[$key] = $val;
				}
				else if (is_array($val) && is_array($array1[$key]))
				{
					\merging($array1[$key], $val);
				}
			}
		}
	
	}
	
	/**
	 * Einrichten der Autoloader
	 *
	 * @return void
	 */
	public function initAutoLoader()
	{
		require_once FRAMEWORK_PATH.'/Loader.php';
		
		$loader = new Core\Loader('Core', ROOT_PATH);
		$loader->register();
		
		$loader = new Core\Loader('jamwork', FRAMEWORKS_PATH);
		$loader->register();

	}
	
	/**
	 * Datenbankverbindung initialisieren und in der Registry hinterlegen
	 *
	 * @return void
	 */
	public function initDatabase()
	{
		// @todo noch sauber machen!
		return;
		$database = new \jamwork\database\MysqlDatabase(
			DB_SERVER,
			DB_USER,
			DB_PASSWORD,
			DB_DATABASE
		);
		
		$dbinit = array(
			'SET NAMES utf8;',
			'SET CHARACTER SET utf8;',
			'SET SESSION character_set_server = utf8;',
			'SET character_set_connection = utf8'
		);
		
		foreach ($dbinit as $sql)
		{
			$query = $database->newQuery()->setQueryOnce($sql);
			$record = $database->newRecordSet()->execute($query);
		}
		
		$reg = Registry::getInstance();
		$reg->setDatabase($database);
	}
	
	/**
	 * Initiates the Debuglogger, but only if we are in dev or staging enviroment
	 *
	 * @return void
	 */
	public function initDebugging()
	{
		$reg = Registry::getInstance();
		$reg->debugger = '';
		
		$session = new HttpSession();
		$reg->setSession($session);
	}
	
	/**
	 * Initialisiere Request und Response Objekt
	 * und hinterlege sie in der Registry
	 *
	 * @return void
	 */
	public function initHttp()
	{
		$reg = Registry::getInstance();
		
		//Request
		$request = new Core\Request($_GET, $_POST, $_SERVER, $_COOKIE);
		$reg->setRequest($request);
		
		//Response
		$response = new Core\Response();
		$response->addHeader('Content-Type', 'text/html; charset=utf-8');
		$reg->setResponse($response);
		
		//Router
		$router = new Core\Router();
		$router->addRoutes(
			array(
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
			)
		);
		$reg->router = $router;
	}
}

//require_once(__DIR__.'/../App/Conf/dbunittest.php');

new InitUnittests();
