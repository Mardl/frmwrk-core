<?php
/**
 * Core\Model-Class
 *
 * PHP version 5.3
 *
 * @category Helper
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */

namespace Core;

/**
 * Core\Model-Class
 *
 * @category Helper
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class Model
{

	/**
	 * Integer value of gender male
	 * @var integer
	 */
	const GENDER_MALE = 1;

	/**
	 * Integer value of gender female
	 * @var integer
	 */
	const GENDER_FEMALE = 2;

	/**
	 * Integer value of gender unknown
	 * @var integer
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
	 * @var ReflectionClass
	 */
	protected $reflectionClass;

	/**
	 * Abfangen von unbekannten Funktionen
	 * Derzeit werden folgende Methode auf Attribute angehandelt
	 * "set..." Wert für das Attribut setzten
	 * "get..." Liefere den Wert
	 * "is..." Vergleiche Wert (Beispiel: $user->isName('John'))
	 * "has..." Prüft ob ein Attribut einen Wert hat (also nicht: null, 0 oder false)
	 *
	 * @param string $name   Name der Methode
	 * @param array  $params Array mit Parametern
	 *
	 * @throws \InvalidArgumentException Wenn das Attribut nicht vorhanden ist
	 * @throws \InvalidArgumentException Wenn die Methode unbekannt ist
	 *
	 * @return mixed
	 */
	public function __call($name, $params)
	{
		$parts = preg_split('/^([a-z]+)/', $name, -1, PREG_SPLIT_DELIM_CAPTURE);
		$prefix = $this->getTablePrefix();
		if (!empty($prefix))
		{
			$parts[2] = str_replace($prefix, '', $parts[2]);
		}

		$method = $parts[1];
		$attribute = lcfirst($parts[2]);



		if (!property_exists($this, $attribute))
		{
			throw new \InvalidArgumentException(
				'Die Klasse '.__CLASS__.' hat das Attribut "'.$attribute.'" nicht'
			);
		}

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
			throw new \InvalidArgumentException(
				'Unbekannte Methode "'.$name.'" in '.__CLASS__
			);
			break;
		}
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

	public function getId()
	{
		return $this->id;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function setDataRow($data = array()){

		if (!empty($data))
		{
			foreach ($data as $key => $value)
			{
				$setter = 'set'.ucfirst($key);
				$this->$setter($value);
			}
		}

	}

	/**
	 * Sorgt dafür, dass das Erstellungsdatum immer ein DateTime-Objekt ist.
	 *
	 * @param DateTime|string $datetime Datetime-Objekt oder String
	 *
	 * @return void
	 */
	public function setCreated($datetime)
	{
		if (!($datetime instanceof \DateTime))
		{
			try
    		{
    			$datetime = new \DateTime($datetime);
    		}
    		catch (\Exception $e)
    		{
    			throw new \InvalidArgumentException('Ungültige Datumsangabe');
    		}
		}

		$this->created = $datetime;
	}

	/**
	 * Sorgt dafür, dass das Erstellungsdatum immer ein DateTime-Objekt ist.
	 *
	 * @param DateTime|string $datetime Datetime-Objekt oder String
	 *
	 * @return void
	 */
	public function setModified($datetime)
	{
		if (!($datetime instanceof \DateTime))
		{
			try
    		{
    			$datetime = new \DateTime($datetime);
    		}
    		catch (\Exception $e)
    		{
    			throw new \InvalidArgumentException('Ungültige Datumsangabe');
    		}
		}

		$this->modified = $datetime;
	}

	/**
	 * @param boolean $new
	 */
	public function setNew($new)
	{
		$this->new=$new;
	}

	/**
	 * @return boolean
	 */
	public function getNew()
	{
		return $this->new;
	}


	/**
	 * Liefert den Tabellenname des Objekts anhand des Klassenkommentars @Table
	 *
	 * @return string|NULL
	 */
	public function getTableName()
	{
		if (!$this->reflectionClass)
		{
			$this->reflectionClass = new \ReflectionClass($this);
		}
		$doc = $this->reflectionClass->getDocComment();

		if (preg_match('/\@Table\((.*)\)/s', $doc, $matches))
		{
			$tmp = substr($matches[1], strpos($matches[1], 'name="'));
			$tmp = substr($tmp, strpos($tmp, '"')+1);
			return substr($tmp, 0, strpos($tmp, '"'));
		}

		return null;
	}

	/**
	 * Liefert den Tabellenname des Objekts anhand des Klassenkommentars @Table
	 *
	 * @return string|NULL
	 */
	public function getTablePrefix()
	{
		if (!$this->reflectionClass)
		{
			$this->reflectionClass = new \ReflectionClass($this);
		}
		$doc = $this->reflectionClass->getDocComment();

		if (preg_match('/\@Prefix\((.*)\)/s', $doc, $matches))
		{
			$tmp = substr($matches[1], strpos($matches[1], 'name="'));
			$tmp = substr($tmp, strpos($tmp, '"')+1);
			return substr($tmp, 0, strpos($tmp, '"'));
		}

		return null;
	}

	public function getIdField()
	{
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
				return $prop->getName();
			}

		}

		throw new \ErrorException("Kein ID-Feld über @Id definiert");
	}

}