<?php
/**
 * Model User
 *
 * PHP version 5.3
 *
 * @category Model
 * @package  Models
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace Core\Application\Models;

use Exception,
	Core\Application\Manager\Directory\Files as FilesManager,
	Core\Model as BaseModel;

/**
 * User
 *
 * @category Model
 * @package  Models
 * @author   Alexander Jonser <alex@dreiwerken.de>
 *
 * @Entity
 * @Table(name="users")
 * @HasLifecycleCallbacks
 */
class User extends BaseModel
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
     * Name (Nickname, Screenname)
     *
     * @var string
     *
     * @Column(type="string", length=64, unique=true)
     */
    protected $username;

    /**
     * First name
     *
     * @var string
     *
     * @Column(type="string", length=32, nullable=true)
     */
    protected $firstname;

    /**
     * Last name
     *
     * @var string
     *
     * @Column(type="string", length=32, nullable=true)
     */
    protected $lastname;

	/**
     * Password
     *
     * @var string
     *
     * @Column(type="string", length=255)
     */
    protected $password;

    /**
     * Email
     *
     * @var string
     *
     * @Column(type="string", length=255, unique=true)
     */
    protected $email;

    /**
     * Is email corrupted
     *
     * @var boolean
     *
     * @Column(type="boolean", name="email_corrupted")
     */
    protected $emailCorrupted = false;

    /**
     * Avatar
     *
     * @var string
     *
     * @Column(type="string", length=255, nullable=true)
     */
    protected $avatar;

    /**
     * Birthday
     *
     * @var DateTime
     *
     * @Column(type="date")
     */
    protected $birthday;

    /**
     * Gender
     *
     * @var integer
     *
     * @Column(type="integer")
     */
    protected $gender;

    /**
     * Created (Registration date)
     *
     * @var DateTime
     *
     * @Column(type="datetime")
     */
    protected $created;

    /**
     * Status
     *
     * @var integer
     *
     * @Column(type="integer")
     */
    protected $status = 1;

    /**
     * Address
     *
     * @var Core\Application\Models\Address
     *
     * @OneToOne(targetEntity="Core\Application\Models\Address", fetch="LAZY", mappedBy="user", cascade={"all"})
     */
    protected $address;

    /**
     * Rights
     *
     * @ManyToMany(targetEntity="App\Models\Right\Group", mappedBy="users", cascade={"persist"})
     * @JoinTable(name="right_group_users")
     */
    protected $rightGroups;

	/**
	 * Einmal-Passwort wurde gesetzt
	 *
	 * @var boolean
	 *
	 * @Column(type="boolean", name="otp")
	 */
	protected $otp = false;

	/**
	 * Administrator
	 *
	 * @var boolean
	 *
	 * @Column(type="boolean")
	 */
	protected $admin = false;

	/**
	 * Language
	 *
	 * @ManyToOne(targetEntity="Core\Application\Models\Language")
	 * @JoinColumn(name="language_id", referencedColumnName="id")
	 */
	protected $language;

	/**
	 * Sets new password.
	 *
	 * @param string $password String mit dem neuen Passwort
	 *
	 * @throws \InvalidArgumentException Wenn das Passwort leer ist
	 * @throws \ErrorException Wenn das Passwort zu kurz ist
	 *
	 * @return string
	 */
    public function setPassword($password, $md5 = true)
    {
        if (empty($password))
        {
        	throw new \InvalidArgumentException('Das Passwort darf nicht leer sein');
        }

        if (strlen($password) < 5)
        {
        	throw new \ErrorException('Das Passwort muss mindestens 5 Zeichen lang sein');
        }

        if ($md5)
        {
        	$this->password = md5($password);
        }
    	else
    	{
    		$this->password = \Core\String::bcryptEncode(
    			$password,
    			md5($this->getId().
					$this->getBirthday()->format('Ymd').
					$this->getGender().
					$this->getCreated()->format("Ymd"))
    		);
    	}
    	return $this->password;
    }

    /**
     * Das Passwort kann man nicht wiederherstellen, deswegen wird ein leerer String
     * zurückgegeben
     *
     * @return string
     *
     */
    public function getPassword()
    {
    	return '';
    }


    /**
     * Sorgt dafür, dass das Geburtsdatum immer ein DateTime-Objekt ist.
     *
     * @param DateTime|string $datetime Datetime-Objekt oder String
     *
     * @return void
     */
    public function setBirthday($datetime)
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

    	$this->birthday = $datetime;
    }

    /**
     * Stellt das Geschlecht auf männlich
     *
     * @return void
     */
    public function setMale()
    {
    	$this->setGender(self::GENDER_MALE);
    }

    /**
     * Stellt das Geschlecht auf weiblich
     *
     * @return void
     */
    public function setFemale()
    {
    	$this->setGender(self::GENDER_FEMALE);
    }

    /**
     * Prüft ob User männlich ist.
     *
     * @return boolean
     */
    public function isMale()
    {
    	if ($this->getGender() == self::GENDER_MALE)
    	{
    		return true;
    	}
    	return false;
    }

    /**
     * Prüft ob User weiblich ist.
     *
     * @return boolean
     */
    public function isFemale()
    {
    	if ($this->getGender() == self::GENDER_FEMALE)
    	{
    		return true;
    	}
    	return false;
    }

    /**
     * Liefert den kompletten Namen
     *
     * @return string
     */
    public function getFullname()
    {
    	return $this->firstname.' '.$this->lastname;
    }

    /**
     * Liefert das Profilbild des Benutzers bzw. abhängig vom Geschlecht ein Placeholder-Foto
     *
     * @return string
     */
    public function getAvatar()
    {
    	if ($this->avatar > 0)
    	{
    		//return $this->avatar;
			$fileModel = FilesManager::getFileById($this->avatar);
			return $fileModel->getThumbnailTarget();
    	}
    	else
    	{
    		$avatar = '/static/images/avatar_';
    		return  $avatar.($this->isMale()?'male.png':'female.png');
    	}
    }

    /**
     * Liefert das Profilbild Object
     *
     * @return object
     */
    public function getAvatarFile()
    {
    	if ($this->avatar > 0)
    	{
    		//return $this->avatar;
    		$fileModel = FilesManager::getFileById($this->avatar);
    		return $fileModel;
    	}
    	return null;
    }

	/**
	 * Liefert die FileId des Benutzerbildes
	 *
	 * @return string
	 */
	public function getAvatarId()
	{
		if ($this->avatar > 0)
		{
			return $this->avatar;
		}
	}

    /**
     * Liefert das Alter des Mitglieds
     *
     * @return string
     */
    public function getAge()
    {
    	$today = new \DateTime();
    	$birthdate = $this->getBirthday();
    	$interval = $today->diff($birthdate);
    	return $interval->format('%y');
    }

    public function setLanguage($language)
    {
    	if (!($language instanceof \Core\Application\Models\Language))
    	{
    		$manager = new \Core\Application\Manager\Language();
    		$language = $manager->getModelById(new \Core\Application\Models\Language(), $language);
    	}

    	$this->language = $language;
    }
}
