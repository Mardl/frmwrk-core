<?php

namespace Core;

/**
 * Class Model
 *
 * @category Core
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class Model
{

	/**
	 * Integer value of gender male
	 * @var int
	 */
	const GENDER_MALE = 1;

	/**
	 * Integer value of gender female
	 * @var int
	 */
	const GENDER_FEMALE = 2;

	/**
	 * Integer value of gender unknown
	 * @var int
	 */
	const GENDER_BOTH = 3;

	/**
	 * Haben sich Daten im Model geändert oder nicht
	 *
	 * @var boolean
	 */
	protected $changed = false;

	/**
	 * Handelt es sich um ein der Datenbank unbekanntes Objekt
	 *
	 * @var boolean
	 */
	protected $new = true;

	/**
	 * Handelt es sich um ein der Datenbank unbekanntes Objekt
	 *
	 * @var \ReflectionClass
	 */
	protected $reflectionClass = false;

	/**
	 * Abfangen von unbekannten Funktionen
	 * Derzeit werden folgende Methode auf Attribute angehandelt
	 * "set..." Wert für das Attribut setzten
	 * "get..." Liefere den Wert
	 * "is..."  Vergleiche Wert (Beispiel: $user->isName('John'))
	 * "has..." Prüft ob ein Attribut einen Wert hat (also nicht: null, 0 oder false)
	 *
	 * @param string $name   Name der Methode
	 * @param array  $params Array mit Parametern
	 * @throws \InvalidArgumentException Wenn das Attribut nicht vorhanden oder die Methode unbekannt ist
	 * @return mixed
	 */

	/**
	 * @var \DateTime
	 */
	protected $modified = '';

	/**
	 * @var \DateTime
	 */
	protected $created = '';

	public function __call($name, $params)
	{
		$parts = preg_split('/^([a-z]+)/', $name, -1, PREG_SPLIT_DELIM_CAPTURE);
		$parts[2] = lcfirst($parts[2]);
		$prefix = $this->getTablePrefix();
		if (!empty($prefix))
		{
			$parts[2] = str_replace($prefix, '', $parts[2]);

			$newMethod = $parts[1] . ucfirst($parts[2]);

			if (method_exists($this, $newMethod))
			{
				return $this->$newMethod($params[0]);
			}
		}

		$method = $parts[1];
		$attribute = $parts[2];

		if (!property_exists($this, $attribute))
		{
			throw new \InvalidArgumentException('Die Klasse ' . __CLASS__ . ' hat das Attribut "' .$prefix . $attribute . '" nicht');
		}

		syslog(LOG_ERR, get_class($this). " {$method}{$attribute}");

		switch ($method)
		{
			case 'set':
				if ($this->$attribute != $params[0])
				{
					$this->$attribute = $params[0];
					$this->changed = true;
				}
				break;
			case 'get':
				return $this->$attribute;
				break;
			case 'has':
				return !empty($this->$attribute);
				break;
			case 'is':
				return ($this->$attribute == $params[0]);
				break;
			default:
				throw new \InvalidArgumentException('Unbekannte Methode "' . $name . '" in ' . __CLASS__);
				break;
		}
	}

	/**
	 * Reflection kann nicht serialisiert werden und muss nach unserialize wieder auf default initialisiert werden.
	 * @return void
	 */
	public function __wakeup()
	{
		$this->reflectionClass = false;
	}

	/**
	 * Überprüft, ob für $name ein Attribut vorhanden ist, oder bei Prefix direkt die Function!
	 *
	 * @param string $name
	 * @return bool
	 *
	 * @deprecated
	 */
	private function existsProperty($name)
	{
		$parts = preg_split('/^([a-z]+)/', $name, -1, PREG_SPLIT_DELIM_CAPTURE);
		$method = $parts[1];
		$attribute = lcfirst($parts[2]);

		$prefix = $this->getTablePrefix();
		if (!empty($prefix))
		{
			$attribute = str_replace($prefix, '', $attribute);

			$newMethod = $method . ucfirst($attribute);
			if (method_exists($this, $newMethod))
			{
				return true;
			}
		}

		if (property_exists($this, $attribute))
		{
			return true;
		}

		return false;
	}

	/**
	 * Konstruktor
	 *
	 * @param array $data Attribut daten
	 */
	public function __construct($data = array())
	{
		$this->setDataRow($data);
		$this->changed = false;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 * @return void
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return bool
	 */
	public function hasId()
	{
		return !empty($this->id);
	}

	/**
	 * @return void
	 */
	public function resetRegisterChange()
	{
		/** Muss in abgeleitender Klasse implementiert werden */
	}
	/**
	 * @param array $data
	 * @return void
	 */
	public function setDataRow($data = array())
	{

		if (!empty($data))
		{
			foreach ($data as $key => $value)
			{
				$setter = 'set' . ucfirst($key);
				$this->$setter($value);
			}
		}
	}

	/**
	 * Überprüft $data nach vorhandene Settern und liefert bereinigtes array zurück
	 *
	 *
	 * @param array $data
	 * @return array
	 */
	public function clearDataRow($data = array())
	{
		/*
		$ret = array();
		if (!empty($data))
		{
			foreach ($data as $key => $value)
			{
				$setter = 'set' . ucfirst($key);
				if ($this->existsProperty($setter))
				{
					$ret[$key] = $value;
				}
			}
		}

		return $ret;
		*/


		$ret = array();
		if (!empty($data))
		{
			foreach ($data as $key => $value)
			{
				$keyToCheck = $key;
				$method = 'set' . ucfirst($keyToCheck);
				$prefix = $this->getTablePrefix();
				if (!empty($prefix)) {
					$keyToCheck = str_replace($prefix, '', $keyToCheck);
					$method = 'set'.ucfirst($keyToCheck);
				}

				if (property_exists($this, $keyToCheck) || method_exists($this, $method))
				{
					$ret[$key] = $value;
				}
			}
		}
		return $ret;
	}

	protected function clearDateTimeSting($datetime)
	{
		return str_replace(':000', '', $datetime);
	}

	/**
	 * Sorgt dafür, dass das Erstellungsdatum immer ein DateTime-Objekt ist.
	 *
	 * @param \DateTime|string $datetime Datetime-Objekt oder String
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	public function setCreated($datetime = 'now')
	{
		if (!($datetime instanceof \DateTime))
		{
			try
			{
				$datetime = new \DateTime($this->clearDateTimeSting($datetime));
			} catch (\Exception $e)
			{
				throw new \InvalidArgumentException('Ungültige Datumsangabe');
			}
		}

		$this->created = $datetime;
	}

	/**
	 * Liefert Create Datetime als mysql Format zurück
	 *
	 * @return string
	 */
	public function getCreatedAsString()
	{
		return $this->getCreated()->format('Y-m-d H:i:s');
	}

	/**
	 * @return mixed
	 */
	public function getCreated()
	{
		if (!($this->created instanceof \DateTime))
		{
			$this->created = new \DateTime(!empty($this->created) ? $this->created : '0000-00-00 00:00:00');
		}
		return $this->created;
	}

	/**
	 * @param int $userId
	 * @return void
	 */
	public function setCreateduser_Id($userId = 0)
	{
		$register = \jamwork\common\Registry::getInstance();

		if (empty($userId) && isset($register->login) && $register->login instanceof \Core\Application\Models\User)
		{
			$this->createduser_id = $register->login->getId();
		}
		else
		{
			$this->createduser_id = !empty($userId) ? $userId : null;
		}
	}

	/**
	 * @return int|null
	 */
	public function getCreateduser_Id()
	{
		return $this->createduser_id;
	}

	/**
	 * Sorgt dafür, dass das Erstellungsdatum immer ein DateTime-Objekt ist.
	 *
	 * @param \DateTime|string $datetime Datetime-Objekt oder String
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	public function setModified($datetime = 'now')
	{
		if (!($datetime instanceof \DateTime))
		{
			try
			{
				$datetime = new \DateTime($this->clearDateTimeSting($datetime));
			} catch (\Exception $e)
			{
				throw new \InvalidArgumentException('Ungültige Datumsangabe');
			}
		}

		$this->modified = $datetime;
	}

	/**
	 * Liefert Modified Datetime als mysql Format zurück
	 *
	 * @return string
	 */
	public function getModifiedAsString()
	{
		return $this->getModified()->format('Y-m-d H:i:s');
	}

	/**
	 * @return mixed
	 */
	public function getModified()
	{
		if (!($this->modified instanceof \DateTime))
		{
			$this->modified = new \DateTime(!empty($this->modified) ? $this->modified : '0000-00-00 00:00:00');
		}
		return $this->modified;
	}

	/**
	 * @param null $userId
	 * @return void
	 */
	public function setModifieduser_Id($userId = null)
	{
		$register = \jamwork\common\Registry::getInstance();
		if (empty($userId) && isset($register->login) && $register->login instanceof \Core\Application\Models\User)
		{
			$this->modifieduser_id = $register->login->getId();

		}
		else
		{
			$this->modifieduser_id = !empty($userId) ? $userId : null;
		}
	}

	/**
	 * @return int|null
	 */
	public function getModifieduser_Id()
	{
		return $this->modifieduser_id;
	}

	/**
	 * @param bool $new
	 * @return void
	 */
	public function setNew($new)
	{
		$this->new = $new;
	}

	/**
	 * @return bool
	 */
	public function getNew()
	{
		return $this->new;
	}

	/**
	 * Liefert den Tabellennamen des Objekts anhand des Klassenkommentars @Table
	 *
	 * @return null|string
	 */
	public function getTableName()
	{
		$tableName = ModelInformation::get(get_class($this), "tablename");

		if (!is_null($tableName))
		{
			if (!($tableName == '-1'))
			{
				return $tableName;
			}

			return '';
		}

		if (!$this->reflectionClass)
		{
			$this->reflectionClass = new \ReflectionClass(get_class($this));
		}
		$doc = $this->reflectionClass->getDocComment();
		$cache = '-1';
		if (preg_match('/\@Table\((.*)\)/s', $doc, $matches))
		{
			$tmp = substr($matches[1], strpos($matches[1], 'name="'));
			$tmp = substr($tmp, strpos($tmp, '"') + 1);
			$tableName = substr($tmp, 0, strpos($tmp, '"'));
			$cache = $tableName;
		}
		ModelInformation::set(get_class($this), "tablename", $cache);

		return $tableName;
	}

	/**
	 * Liefert den Prefix des Tabellennamens anhand @Prefix zurück
	 *
	 * @return null|string
	 */
	public function getTablePrefix()
	{
		$prefix = ModelInformation::get(get_class($this), "prefix");

		if (!is_null($prefix))
		{
			if (!($prefix == '-1'))
			{
				return $prefix;
			}

			return '';
		}

		if (!$this->reflectionClass)
		{
			$this->reflectionClass = new \ReflectionClass($this);
		}

		$doc = $this->reflectionClass->getDocComment();

		$cache = '-1';
		if (preg_match('/\@Prefix\((.*)\)/s', $doc, $matches))
		{
			$tmp = substr($matches[1], strpos($matches[1], 'name="'));
			$tmp = substr($tmp, strpos($tmp, '"') + 1);
			$prefix = substr($tmp, 0, strpos($tmp, '"'));
			$cache = $prefix;
		}
		ModelInformation::set(get_class($this), "prefix", $cache);

		return $prefix;
	}

	/**
	 * @return null|string
	 * @throws \ErrorException
	 */
	public function getIdField()
	{
		$id = ModelInformation::get(get_class($this), "idfield");

		if (!is_null($id))
		{
			return $id;
		}

		if (!$this->reflectionClass)
		{
			$this->reflectionClass = new \ReflectionClass($this);
		}
		$properties = $this->reflectionClass->getProperties();

		foreach ($properties as $prop)
		{
			$doc = $prop->getDocComment();

			if (preg_match('/\@Id/s', $doc, $matches))
			{
				$id = $this->getTablePrefix() . $prop->getName();
				ModelInformation::set(get_class($this), "idfield", $id);

				return $id;
			}
		}

		throw new \ErrorException("Kein ID-Feld über @Id definiert");
	}
}
