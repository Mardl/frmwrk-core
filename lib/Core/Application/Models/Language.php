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
 * @Entity
 * @Table(name="languages")
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
     * Volltext
     *
     * @var string
     *
     * @Column(type="string", length=32, nullable=false)
     */
    protected $volltext;

    /**
     * Isocode
     *
     * @var string
     *
     * @Column(type="string", length=32, nullable=true)
     */
    protected $isocode;

    /**
     * Länderkürzel
     *
     * @var string
     *
     * @Column(type="string", length=16, nullable=true)
     */
    protected $country;


    public function getDataRow()
    {
    	$data = array(
    		'id'		=> $this->getId(),
    		'volltext'	=> $this->getVolltext(),
    		'isocode' 	=> $this->getIsocode(),
    		'country' 	=> $this->getCountry()
    	);

    	return $data;
    }

}
