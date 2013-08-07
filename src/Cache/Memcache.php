<?php

namespace Core\Cache;

use \Memcached as BaseCache;

/**
 * Memcache
 *
 * @category Cache
 * @package  Core\Cache
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class Memcache
{

	/**
	 * Instance-Keeper
	 *
	 * @var \Core\Cache\Memcache
	 */
	private static $instance = null;

	/**
	 * Memached-Instance
	 *
	 * @var \Memcached
	 */
	private $memcache = null;


	/**
	 * Liefert die Instanz des Singleton
	 *
	 * @return Core\Cache\Memcache
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Konstruktor
	 *
	 * Wenn der Cache aktiviert ist und die Klasse Memcached existiert wird
	 * zur private Instanz eine Verbindung zum Memcache aufgebaut und die TTL,
	 * falls nicht über Config gesetzt, auf 10 Sekunden eingerichtet.
	 */
	private function __construct()
	{
		if (class_exists('\Memcached') && CACHE_ENABLED && !defined('DISABLE_CACHE'))
		{
			$this->memcache = new BaseCache();

			if (defined('CACHE_TTL') && !defined('UPDATE_TTL'))
			{
				$this->ttl = CACHE_TTL;
			}
			else
			{
				if (defined('CACHE_TTL') && defined('UPDATE_TTL'))
				{
					$this->ttl = UPDATE_TTL;
				}
				else
				{
					$this->ttl = 10;
				}
			}

			$this->memcache->addServer('localhost', 11211);
		}
	}

	/**
	 * Fügt einen Eintrag dem Memcache hinzu
	 *
	 * @param string $key   Cache-Schlüssel
	 * @param mixed  $value Zu speichender Wert
	 * @param int    $ttl   Optionale Angabe der Gültigkeit
	 * @return void
	 */
	public function add($key, $value, $ttl = null)
	{
		if ($this->memcache)
		{
			if (is_null($ttl))
			{
				$ttl = $this->ttl;
			}

			$this->memcache->add($key, $value, $ttl);
		}
	}

	/**
	 * Liefert den Value des Cache-Key oder FALSE wenn der Eintrag ungültig ist.
	 * Die Funktion liefert auch FALSE wenn der Cache nicht aktiv ist.
	 *
	 * @param string $key Cache-Key
	 * @return bool|mixed
	 */
	public function get($key)
	{
		if ($this->memcache)
		{
			return $this->memcache->get($key);
		}

		return false;
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function remove($key)
	{
		if ($this->memcache)
		{
			return $this->memcache->delete($key);
		}
	}

	/**
	 * @return array
	 */
	public function getKeys()
	{
		$memcache = memcache_connect('localhost', 11211);

		$list = array();
		$allSlabs = $memcache->getExtendedStats('slabs');

		foreach ($allSlabs as $server => $slabs)
		{
			foreach ($slabs as $slabId => $slabMeta)
			{
				if (!is_numeric($slabId))
				{
					continue;
				}

				$cdump = $memcache->getExtendedStats('cachedump', (int)$slabId, 99999999);

				foreach ($cdump as $server => $entries)
				{
					if (!$entries)
					{
						continue;
					}

					foreach ($entries as $eName => $eData)
					{
						$value = $this->get($eName);

						if ($value !== false)
						{
							$list[] = $eName;
						}
					}
				}
			}
		}

		return $list;
	}

	/**
	 * @param string $prefix
	 * @return void
	 */
	public function truncateByKeyPrefix($prefix)
	{
		if ($this->memcache)
		{
			$keys = $this->getKeys();
			foreach ($keys as $key)
			{
				if (substr($key, 0, strlen($prefix)) == $prefix)
				{
					$this->remove($key);
				}
			}
		}
	}
}
