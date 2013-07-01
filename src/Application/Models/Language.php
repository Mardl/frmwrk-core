<?php
namespace Core\Application\Models;

use Core\Model as BaseModel;

/**
 * Class Language
 *
 * @category Core
 * @package  Core\Application\Models
 * @author   Alexander Jonser <alex@dreiwerken.de>
 *
 * @method string getIsocode()
 * @method string getCountryCode()
 * @method string getInternational()
 * @method string getNational()
 *
 * @method setIsocode($value)
 * @method setCountryCode($value)
 * @method setInternational($value)
 * @method setNational($value)
 *
 * @MappedSuperclass
 */
class Language extends BaseModel implements \Core\Application\Interfaces\ModelsInterface
{

	/**
	 * Id
	 *
	 * @var integer
	 *
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * Kurzversion D, I, NL ... however
	 *
	 *
	 * @var string
	 *
	 * @Column(type="string", length=8)
	 */
	protected $short;


	/**
	 * Landesspezifischer Name
	 *
	 * @var string
	 *
	 * @Column(type="string", length=50, nullable=true)
	 */
	protected $national;

	/**
	 * Internationaler Name
	 *
	 * @var string
	 *
	 * @Column(type="string", length=50)
	 */
	protected $international;

	/**
	 * Iso-Code from ISO-639-1
	 *
	 * @var string
	 *
	 * @Column(type="string", length=10)
	 */
	protected $isocode;

	/**
	 * Iso-Code from ISO-3166-1
	 *
	 * @var string
	 *
	 * @Column(type="string", length=10)
	 */
	protected $countryCode;

	/**
	 * @return array
	 */
	public function getDataRow()
	{
		$data = array(
			'id' => $this->getId(),
			'isocode' => $this->getIsocode(),
			'countryCode' => $this->getCountryCode(),
			'international' => $this->getInternational(),
			'national' => $this->getNational()
		);

		return $data;
	}

	/**
	 * Liefert Länder-Sprachkennzeichen
	 * Beispiel de-de oder de-AT
	 *
	 * @return string
	 */
	public function getHtmlLanguage()
	{
		return $this->getIsocode() . '-' . $this->getCountryCode();
	}
}
