<?php
/**
 * Core\Loader-Class
 *
 * PHP version 5.3
 *
 * @category Helper
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */

namespace Core;

/**
 * Loader
 * 
 * @category Helper
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class Loader
{

	/**
	 * Responsible namespace
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * Path to framework
	 *
	 * @var string
	 */
	protected $path;
	
	/**
	 * Legt fest ob beim Nichtauffinden einer Klasse eine Exception geworfen werden soll
	 * oder nicht
	 *
	 * @var string
	 */
	protected $exception;

	/**
	 * @var array
	 */
	protected $replace = array(
		'_'  => DIRECTORY_SEPARATOR,
		'\\' => DIRECTORY_SEPARATOR
	);

	/**
	 * Construct
	 *
	 * @param string  $namespace Name des Namespaces
	 * @param string  $path      Pfad des Namespace
	 * @param boolean $exception Definiert ob eine Exception geworfen werden soll, wenn die Klasse nicht vorhanden ist
	 */
	public function __construct($namespace, $path, $exception = false)
	{
		$this->namespace = $namespace;
		$this->path = $path;
		$this->exception = $exception;
	}

	/**
	 * Register autoload
	 *
	 * @return boolean
	 */
	public function register() 
	{
		return spl_autoload_register(array($this, '_autoload'));

	}

	/**
	 * Load a class
	 *
	 * @param string $className Class name.
	 * 
	 * @throws Exception wenn die PHP-Datei nicht gefunden wird.
	 * 
	 * @return void
	 */
	private function _autoload($className)
	{

		if (substr($className, 0, strlen($this->namespace)) != $this->namespace)
		{ 
			return false;
		}
		
		$file = $this->path.'/'.trim(strtr($className, $this->replace), '_\\');
		$php = false;
		$inc = false;
		
		if (file_exists($file.'.php'))
		{
			$php = true;
		} 
		else if (file_exists($file.'.inc'))
		{
			$inc = true;
		}
		
		if ($php == true)
		{
			require_once $file.'.php';
		}
		else if ($inc == true)
		{
			require_once $file.'.inc';
		}
		else
		{
			syslog(LOG_ALERT, "Klasse $className (Pfad: $file) wurde nicht gefunden");
			if ($this->exception)
			{
				throw new \ErrorException(
					"Klasse $className (Pfad: $file) wurde nicht gefunden", 
					404
				);
			}		
		}
		
		
	}

}