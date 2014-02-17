<?php

namespace Core;

/**
 * Class Config
 *
 * @category Core
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class Config
{

	/**
	 * Speichert zu den angegebenen Domains die entsprechenden Config-Dateinamen
	 *
	 * @var array
	 */
	protected $_domains = array();

	/**
	 * Speichert die Konfigurationsvariablen
	 *
	 * @var array
	 */
	protected $_vars = array();

	/**
	 * @var string
	 */
	protected $_configFolder;

	/**
	 * Baut den Pfad zur Config auf
	 */
	public function __construct()
	{
		$this->_configFolder = sprintf('%s/Conf', APPLICATION_PATH);
	}

	/**
	 * Fügt die Zuweisung von Hostname zu Config-File dem internen Array zu
	 *
	 * @param string $domain   Domainname
	 * @param string $fileName Name der Konfigurationsdatei
	 * @return void
	 */
	public function add($domain, $fileName)
	{
		$this->_domains[$domain] = $fileName;
	}

	/**
	 * Löst den Hostnamen auf und liefert ihn zurück. Sollte der Hostname nicht gefunden werden, liefert
	 * die Methode false.
	 *
	 * @return string|false
	 */
	protected function resolveHostname()
	{
		return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : false;
	}

	/**
	 * Liefert zurück, ob SSL benutzt werden soll
	 *
	 * @return bool
	 */
	protected function useSSL()
	{
		return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
	}

	/**
	 * Liefert zurück, ob für diesen Host eine SSL Config existiert
	 *
	 * @param string $file
	 *
	 * @return bool
	 */
	protected function hasSSLConfig($file)
	{
		$file = sprintf('%s/%s', $this->_configFolder, $this->getConfigFileWithSSLPattern($file));
		return file_exists($file);
	}

	/**
	 * Liefert den Namen einer SSL Config Datei anhand des Filenamens
	 *
	 * @param string $file
	 *
	 * @return strinfg
	 */
	protected function getConfigFileWithSSLPattern($file)
	{
		return sprintf('ssl.%s', $file);
	}

	/**
	 * Liefert alle möglichen Config Files zurück
	 *
	 * @param string $host
	 *
	 * @return string
	 */
	protected function seekConfigFiles($host)
	{
		$files = array();

		foreach ($this->_domains as $domain => $file)
		{
			if (!$this->_hostEndsWith($host, $domain) && $domain !== 'general')
			{
				continue;
			}

			$files[] = $file;
			if ($this->useSSL() && $this->hasSSLConfig($file))
			{
				$files[] = $this->getConfigFileWithSSLPattern($file);
			}
		}

		return $files;
	}

	/**
	 * @param $amount
	 */
	protected function hasEnoughConfigs($amount)
	{
		return $amount >= 2;
	}

	/**
	 * Erzeugt aus den $configVariables ein Array oder speichert es in das $_vars Array
	 *
	 * @param array $configVariables
	 */
	protected function makeGlobal(array $configVariables)
	{
		foreach ($configVariables as $key => $value)
		{
			if (is_scalar($value) && !defined($key))
			{
				define($key, $value);
			}
			else
			{
				$this->_vars[$key] = $value;
			}
		}
	}

	/**
	 * Bindet alle Configs vom files array ein
	 *
	 * @param array $files
	 */
	protected function loadConfigFromFS(array $files)
	{
		$configVariables = array();

		foreach ($files as $file)
		{
			if (!file_exists(sprintf('%s/%s', $this->_configFolder, $file)))
			{
				continue;
			}

			require sprintf('%s/%s', $this->_configFolder, $file);
			\merging($configVariables, $config);
		}

		$this->makeGlobal($configVariables);
	}

	/**
	 * Lädt die Konfigurationsvariablen und speichert sie zwischen.
	 * Wenn eine Konfigurationsvariable KEIN Array ist, wird sie als globale Konstante definiert.
	 *
	 * @param string $host die host datei die geladen werden soll
	 *
	 * @return void
	 * @throws \InvalidArgumentException
	 * @throws \LengthException
	 */
	public function load($host = false)
	{
		if ($host === false) {
			$host = $this->resolveHostname();
			if ($host === false) {
				throw new \InvalidArgumentException('Kein Hostname gefunden');
			}
		}

		$host = sprintf('.%s', $host);
		$host = trim($host, '.');

		// Die Dateien existieren nicht zwangsläufig
		$files  = $this->seekConfigFiles($host);
		if (!$this->hasEnoughConfigs(count($files)))
		{
			throw new \LengthException('Keine Konfiguration für den Host gefunden');
		}

		$this->loadConfigFromFS($files);
	}

	/**
	 * Prüft ob der aktuelle Hostname mit dem angegebenen übereinstimmt.
	 *
	 * @param string $haystack Aktueller Hostname
	 * @param string $needle   Vergleichswert
	 * @return bool
	 */
	protected function _hostEndsWith($haystack, $needle)
	{
		return \Core\String::endsWith($haystack, $needle);
	}

	/**
	 * Magische Funktion um Daten aus der Config zu bekommen.
	 * Liefert entweder den Wert für den angegeben Konfigurationsparamter, wenn dieser vorhanden ist, ansonsten den Wert null;
	 *
	 * @param string|int $name Index
	 * @return mixed|null
	 */
	public function __get($name)
	{
		if (isset($this->_vars[$name]))
		{
			return $this->_vars[$name];
		}

		return null;
	}
}
