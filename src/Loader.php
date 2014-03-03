<?php

namespace Core;

/**
 * Class Loader
 *
 * @category Core
 * @package  Core
 * @author   @author   Alexander Jonser <alex@dreiwerken.de>
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
	 * Legt fest, ob beim Nichtauffinden einer Klasse eine Exception geworfen werden soll oder nicht
	 *
	 * @var boolean
	 */
	protected $exception;

	/**
	 * Legt fest ob der Namespace aus einem phar-Archive geladen wird
	 *
	 * @var boolean
	 */
	protected $pharArchive = false;

	protected $pharloaded = false;

	/**
	 * @var array
	 */
	protected $replace = array(
		'_' => DIRECTORY_SEPARATOR,
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

		$this->pharArchive = (substr($path, -5) == '.phar');

		if ($this->pharArchive && !file_exists($this->path))
		{
			$this->exception = true;
			$this->error('', '', "Das angegebene PHAR-Archive '".$this->path."' existiert nicht!");
		}
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
	 * @param string $className Class name
	 * @return bool
	 * @throws \ErrorException Wenn die PHP-Datei nicht gefunden wird
	 */
	public function _autoload($className)
	{
		if (substr($className, 0, strlen($this->namespace)) != $this->namespace)
		{
			return false;
		}

		if ($this->pharArchive)
		{
			return $this->loadFromPhar($className);
		}

		$this->loadFromFilesystem($className);

	}

	/**
	 * Versucht die Klasse aus einem PHAR Archiv zu laden
	 *
	 * @param string $classname
	 */
	protected function loadFromPhar($className)
	{
		$alias = $this->namespace.'.phar';
		$file = trim(strtr($className, $this->replace), '_\\');
		$file = str_replace($this->namespace.'/','', $file);

		if (!$this->pharloaded)
		{
			$this->pharloaded = \Phar::loadPhar($this->path, $alias);
		}

		$srcPhar = "phar://".$alias.'/'.$file.'.php';
		if (!@include_once($srcPhar))
		{
			$this->error($className, $srcPhar);
		}

	}

	/**
	 * Versucht die Klasse vom Dateisystem zu laden
	 *
	 * @param string $classname
	 */
	protected function loadFromFilesystem($className)
	{
		$file = $this->path . '/' . trim(strtr($className, $this->replace), '_\\');

		$classNameSrc = str_replace($this->namespace, '', $className);
		$fileSrc = $this->path . '/' . $this->namespace . '/src' . trim(strtr($classNameSrc, $this->replace), '_\\');
		$fileRootSrc = $this->path . '/src' . trim(strtr($classNameSrc, $this->replace), '_\\');



		$php = false;
		$inc = false;

		/*
		if (file_exists($file . '.php'))
		{
			$php = true;
		}
		else if (file_exists($file . '.inc'))
		{
			$inc = true;
		}


		*/
		if (file_exists($file . '.php'))
		{
			$php = true;
		}
		elseif (file_exists($file . '.inc'))
		{
			$inc = true;
		}
		elseif (file_exists($fileSrc . '.php'))
		{
			$php = true;
			$file = $fileSrc;
		}
		elseif (file_exists($fileSrc . '.inc'))
		{
			$inc = true;
			$file = $fileSrc;
		}
		elseif (file_exists($fileRootSrc . '.php'))
		{
			$php = true;
			$file = $fileRootSrc;
		}
		elseif (file_exists($fileRootSrc . '.inc'))
		{
			$inc = true;
			$file = $fileRootSrc;
		}


		if ($php == true)
		{
			require_once $file . '.php';
			return;
		}
		else if ($inc == true)
		{
			require_once $file . '.inc';
			return;
		}

		$this->error($className, $file);

	}

	/**
	 * Sendet die Fehlermeldung, dass die Datei nicht gefunden wurde ans syslog.
	 * Wenn Exceptions aktiviert, dann werfe zusätzlich noch eine ErrorException
	 *
	 * @param string $className
	 * @param string $file
	 *
	 * @throws \ErrorException Wenn Exception aktiviert
	 */
	protected function error($className, $file, $message = null)
	{
		if ($message == null)
		{
			$message = "Klasse $className (Pfad: $file) wurde nicht gefunden";
		}

		syslog(LOG_ALERT, $message);
		if ($this->exception)
		{
			throw new \ErrorException($message, 404);
		}
	}

}
