<?php

namespace Core\Application\Models;

use Exception, Core\Application\Manager\Directory\Files as FilesManager, Core\Model as BaseModel;

/**
 * Class User
 *
 * @category Core
 * @package  Core\Application\Models
 * @author   Alexander Jonser <alex@dreiwerken.de>
 *
 *
 * @MappedSuperclass
 */
class User extends BaseModel
{

	/**
	 * Id
	 *
	 * @var int
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
	 * @Column(type="string", length=255)
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
	 * @var \DateTime
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
	protected $gender = 0;

	/**
	 * Created (Registration date)
	 *
	 * @var \DateTime
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
	 * @var \Core\Application\Models\Address
	 *
	 * @OneToOne(targetEntity="\Core\Application\Models\Address", fetch="LAZY", mappedBy="user", cascade={"all"})
	 */
	protected $address;

	/**
	 * Einmal-Passwort wurde gesetzt
	 *
	 * @var bool
	 *
	 * @Column(type="boolean", name="otp")
	 */
	protected $otp = false;

	/**
	 * Administrator
	 *
	 * @var bool
	 *
	 * @Column(type="boolean")
	 */
	protected $admin = false;


	/**
	 * Language
	 *
	 * @ManyToOne(targetEntity="App\Models\Language")
	 *
	 */
	protected $language = null;

	/**
	 * Setzt das neue Passwort
	 *
	 * @param string $password String mit dem neuen Passwort
	 * @param bool   $md5      Kodierung mit MD5
	 * @return string
	 * @throws \ErrorException
	 * @throws \InvalidArgumentException
	 */
	public function setPassword($password, $md5 = true)
	{
		if (empty($password))
		{
			throw new \InvalidArgumentException('Das Passwort darf nicht leer sein!');
		}

		if (strlen($password) < 5)
		{
			throw new \ErrorException('Das Passwort muss mindestens 5 Zeichen lang sein!');
		}

		if ($md5)
		{
			$this->password = md5($password);
		}
		else
		{
			$this->password = \Core\String::bcryptEncode($password, md5($this->getId() . $this->getBirthday()->format('Ymd') . $this->getGender() . $this->getCreated()->format("Ymd")));
		}

		return $this->password;
	}

	/**
	 * Das Passwort kann man nicht wiederherstellen, deswegen wird ein leerer String zurückgegeben
	 *
	 * @return string
	 *
	 */
	public function getPassword()
	{
		return '';
	}

	/**
	 * Sorgt dafür, dass das Geburtsdatum immer ein DateTime-Objekt ist
	 *
	 * @param \DateTime|string $datetime DateTime-Objekt oder String
	 * @return void
	 * @throws \InvalidArgumentException
	 */
	public function setBirthday($datetime)
	{
		if (!($datetime instanceof \DateTime))
		{
			try
			{
				$datetime = new \DateTime($this->clearDateTimeSting($datetime));
			} catch (\Exception $e)
			{
				throw new \InvalidArgumentException('Ungültige Datumsangabe!');
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
	 * Prüft, ob User männlich ist.
	 *
	 * @return bool
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
	 * Prüft, ob User weiblich ist.
	 *
	 * @return bool
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
		return $this->firstname . ' ' . $this->lastname;
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
			$fileModel = FilesManager::getFileById($this->avatar);

			return $fileModel->getThumbnailTarget();
		}
		else
		{
			$avatar = 'static/images/avatar_';

			return $avatar . ($this->isMale() ? 'male.png' : 'female.png');
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

	/**
	 * Setzt die Sprache
	 *
	 * @param int|\Core\Application\Models\Language $language
	 * @return void
	 */
	public function setLanguage($language)
	{
		if (class_exists('Core\Application\Models\Language', false) && !($language instanceof \Core\Application\Models\Language) && $language !== null)
		{
			if (class_exists('\App\Manager\Language') && class_exists('\App\Models\Language'))
			{
				$manager = new \App\Manager\Language();
				$language = $manager->getModelById(new \App\Models\Language(), $language);
			}
			else
			{
				$manager = new \Core\Application\Manager\Language();
				$language = $manager->getModelById(new \Core\Application\Models\Language(), $language);
			}
		}


		$this->language = $language;
	}

	/**
	 * Liefert die ID der Sprache
	 *
	 * @return int|null
	 */
	public function getLanguageId()
	{
		if ($this->language instanceof \Core\Application\Models\Language)
		{
			return $this->language->getId();
		}

		return $this->language;
	}

	/**
	 * @param string $username
	 * @return void
	 */
	public function setUsername($username)
	{
		$this->username = $username;
	}

	/**
	 * @param string $firstname
	 * @return void
	 */
	public function setFirstname($firstname)
	{
		$this->firstname = $firstname;
	}

	/**
	 * @param string $lastname
	 * @return void
	 */
	public function setLastname($lastname)
	{
		$this->lastname = $lastname;
	}

	/**
	 * @param string $email
	 * @return void
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}

	/**
	 * @param boolean $emailCorrupted
	 * @return void
	 */
	public function setEmailCorrupted($emailCorrupted)
	{
		$this->emailCorrupted = $emailCorrupted;
	}

	/**
	 * @param number $gender
	 * @throws \InvalidArgumentException
	 * @return void
	 */
	public function setGender($gender)
	{
		if ($gender != self::GENDER_FEMALE && $gender != self::GENDER_MALE && $gender != self::GENDER_BOTH)
		{

			syslog(LOG_ERR, "Gender: $gender, ID: ".$this->id);
			#throw new \InvalidArgumentException("Ungültige Geschlechtsangabe ($gender)");
		}
		$this->gender = $gender;
	}

	/**
	 * @param number $status
	 * @return void
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}

	/**
	 * @param boolean $otp
	 * @return void
	 */
	public function setOtp($otp)
	{
		$this->otp = $otp;
	}

	/**
	 * @param boolean $admin
	 * @return void
	 */
	public function setAdmin($admin)
	{
		$this->admin = $admin;
	}

	/**
	 * @param \Core\Application\Models\Address $address
	 * @return void
	 */
	public function setAddress($address)
	{
		$this->address = $address;
	}

	/**
	 * @return \Core\Application\Models\Address
	 */
	public function getAddress()
	{
		return $this->address;
	}

	/**
	 * @return boolean
	 */
	public function getAdmin()
	{
		return $this->admin;
	}

	/**
	 * @return \DateTime
	 */
	public function getBirthday()
	{
		return $this->birthday;
	}

	/**
	 * @return string
	 */
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * @return boolean
	 */
	public function getEmailCorrupted()
	{
		return $this->emailCorrupted;
	}

	/**
	 * @return string
	 */
	public function getFirstname()
	{
		return $this->firstname;
	}

	/**
	 * @return int
	 */
	public function getGender()
	{
		return $this->gender;
	}

	/**
	 * @return mixed
	 */
	public function getLanguage()
	{
		return $this->language;
	}

	/**
	 * @return string
	 */
	public function getLastname()
	{
		return $this->lastname;
	}

	/**
	 * @return boolean
	 */
	public function getOtp()
	{
		return $this->otp;
	}

	/**
	 * @return int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->username;
	}

	/**
	 * @param string $avatar
	 * @return void
	 */
	public function setAvatar($avatar)
	{
		$this->avatar = $avatar;
	}


}
