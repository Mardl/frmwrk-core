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
	 * Lädt die Konfigurationsvariablen und speichert sie zwischen.
	 * Wenn eine Konfigurationsvariable KEIN Array ist, wird sie als globale Konstante definiert.
	 *
	 * @return void
	 * @throws \InvalidArgumentException
	 * @throws \LengthException
	 */
	public function load()
	{
		if (!isset($_SERVER['HTTP_HOST']) || !is_string($_SERVER['HTTP_HOST']))
		{
			throw new \InvalidArgumentException('Unbekannter Hostname');
		}

		$configsLoaded = 0;
		$hostname = '.' . trim($_SERVER['HTTP_HOST'], '.');
		$conf = array();
		$configurations = array();

		foreach ($this->_domains as $domain => $fileName)
		{
			if ($this->_hostEndsWith($hostname, $domain) || $domain == 'general')
			{
				$configurations[] = $fileName;
				if (array_key_exists('HTTPS', $_SERVER) && ($_SERVER['HTTPS'] == "on")){
					if (file_exists(APPLICATION_PATH . '/Conf/ssl.'.$fileName)){
						$configurations[] = "ssl.".$fileName;
					}
				}
			}
		}

		foreach ($configurations as $fileName)
		{
			require APPLICATION_PATH . '/Conf/' . $fileName;
			\merging($conf, $config);

			$configsLoaded++;
		}

		if ($configsLoaded < 2)
		{
			throw new \LengthException('Keine Konfiguration für den Host ' . $hostname . ' gefunden');
		}

		foreach ($conf as $key => $value)
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
	 * Prüft ob der aktuelle Hostname mit dem angegebenen übereinstimmt.
	 *
	 * @param string $haystack Aktueller Hostname
	 * @param string $needle   Vergleichswert
	 * @return bool
	 */
	protected function _hostEndsWith($haystack, $needle)
	{
		if (substr($haystack, -strlen($needle)) == $needle)
		{
			return true;
		}
		else
		{
			return false;
		}
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
