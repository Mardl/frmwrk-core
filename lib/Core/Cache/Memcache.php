<?php
/**
 * Core\Cache\Memcache-Class
 *
 * PHP version 5.3
 *
 * @category Cache
 * @package  Core\Cache
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
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
	 * @var Core\Cache\Memcache
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
	 * Wenn der Cache aktiviert ist und die Klasse Memcached existiert wird
	 * zur private Instanz eine Verbindung zum Memcache aufgebaut und die TTL, 
	 * falls nicht über Config gesetzt, auf 10 Sekunden eingerichtet.
	 */
	private function __construct()
	{
		
		if (class_exists('\Memcached') && CACHE_ENABLED && !defined('DISABLE_CACHE'))
		{
			
			
			$this->memcache = new BaseCache();
			
			if (defined('CACHE_TTL'))
			{
				$this->ttl = CACHE_TTL;
			}
			else
			{
				$this->ttl = 10;
			}
			
			$this->memcache->addServer('localhost', 11211);
		}
	}
	
	/**
	 * Fügt einen Eintrag dem Memcache hinzu
	 * 
	 * @param string  $key   Cache-Schlüssel
	 * @param mixed   $value Zu speichender Wert
	 * @param integer $ttl   Optionale Angabe der Gültigkeit
	 * 
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
	 * 
	 * @return mixed|boolean
	 */
	public function get($key)
	{
		if ($this->memcache)
		{
			return $this->memcache->get($key);
		}
		return false;
	}
	
}
?>