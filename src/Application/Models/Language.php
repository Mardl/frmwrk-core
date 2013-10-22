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

	/**
	 * @param string $countryCode
	 * @return void
	 */
	public function setCountryCode($countryCode)
	{
		$this->countryCode = $countryCode;
	}

	/**
	 * @return string
	 */
	public function getCountryCode()
	{
		return $this->countryCode;
	}

	/**
	 * @param string $international
	 * @return void
	 */
	public function setInternational($international)
	{
		$this->international = $international;
	}

	/**
	 * @return string
	 */
	public function getInternational()
	{
		return $this->international;
	}

	/**
	 * @param string $isocode
	 * @return void
	 */
	public function setIsocode($isocode)
	{
		$this->isocode = $isocode;
	}

	/**
	 * @return string
	 */
	public function getIsocode()
	{
		return $this->isocode;
	}

	/**
	 * @param string $national
	 * @return void
	 */
	public function setNational($national)
	{
		$this->national = $national;
	}

	/**
	 * @return string
	 */
	public function getNational()
	{
		return $this->national;
	}

	/**
	 * @param string $short
	 * @return void
	 */
	public function setShort($short)
	{
		$this->short = $short;
	}

	/**
	 * @return string
	 */
	public function getShort()
	{
		return $this->short;
	}


}
