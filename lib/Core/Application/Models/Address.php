<?php
/**
 * Model Address
 *
 * PHP version 5.3
 *
 * @category Model
 * @package  Models
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace App\Models;

use Core\Model as BaseModel;

/**
 * Address
 *
 * @category Model
 * @package  Models
 * @author   Alexander Jonser <alex@dreiwerken.de>
 *
 * @Entity
 * @Table(name="addresses")
 */
class Address extends BaseModel
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
     * Street
     *
     * @var string
     *
     * @Column(type="string", length=32, nullable=true)
     */
    protected $street;

    /**
     * City
     *
     * @var string
     *
     * @Column(type="string", length=32, nullable=true)
     */
    protected $city;

    /**
     * Zipcode
     *
     * @var string
     *
     * @Column(type="string", length=16, nullable=true)
     */
    protected $zipcode;

    /**
     * Province
     *
     * @var string
     *
     * @Column(type="string", length=32, nullable=true)
     */
    protected $province;
	
    /**
     * User
     *
     * @var App\Model\User
     *
     * @OneToOne(targetEntity="App\Models\User", fetch="LAZY", inversedBy="address")
     */
    protected $user;
    
}