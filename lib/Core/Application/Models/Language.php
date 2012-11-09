<?php
/**
 * Model Language
 *
 * PHP version 5.3
 *
 * @category Model
 * @package  Models
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace Core\Application\Models;

use Core\Model as BaseModel;

/**
 * Language
 *
 * @category Model
 * @package  Models
 * @author   Alexander Jonser <alex@dreiwerken.de>
 *
 * @method string getInternational()
 * @method string getNational()
 * @method string getIsocode()
 * @method string getCountryCode()
 *
 * @method setInternational($value)
 * @method setNational($value)
 * @method setIsocode($value)
 * @method setCountryCode($value)
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
	 *
	 * Iso-Code from ISO-3166-1
	 *
     *
     * @var string
     *
     * @Column(type="string", length=10)
     */
    protected $countryCode;



	public function getDataRow()
    {
    	$data = array(
    		'id'			=> $this->getId(),
			'international' => $this->getInternational(),
			'national'		=> $this->getNational(),
			'countrycode'	=> $this->getCountryCode(),
			'isocode' 		=> $this->getIsocode()
		);

    	return $data;
    }

	/**
	 *
	 * Liefert Länder-Sprachkennzeichen
	 * Beispiel de-de oder de-AT
	 *
	 * @return string
	 */
	public function getHtmlLanguage()
	{
		return $this->getIsocode().'-'.$this->getCountryCode();
	}
}