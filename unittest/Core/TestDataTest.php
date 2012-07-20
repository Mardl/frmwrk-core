<?php
/**
 * Unittest für Coverage
 * Hilfstest, der alle Klasse für jeden Testlauf einmal inistanziert
 *
 * PHP version 5.3
 *
 * @category Unittest
 * @package  Core
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
namespace unittest\lifemeter;

use jamwork\common\Registry,
	jamwork\database\MysqlRecordset as Recordset,
	App\Models\User as UserModel,
	App\Models\Scale as ScaleModel,
	App\Manager\User as UserManager,
	App\Manager\Scale as ScaleManager,
	Lifemeter\ObjectFactory;

/**
 * Coverage test case.
 * 
 * @category Unittest
 * @package  Main
 * @author   Alexander Jonser <alex@dreiwerken.de>
 */
class TestDataTest extends \PHPUnit_Framework_TestCase
{
	
	/**
	 * Constructs the test case.
	 */
	public function __construct()
	{
		
	}

	/**
	 * Löschen der DB-Tabellen und neu aufsetzen
	 * 
	 * @return void
	 */
	public function testDropAndRecreateDatabase()
	{
		$arr = array();
		
		exec('framework/doctoolUnittesting orm:schema-tool:drop --force', $arr, $success);
		if ($success !== 0)
		{
			die("Error occured by dropping tables");
		}
		
		exec('framework/doctoolUnittesting orm:schema-tool:update --force', $arr, $success);
		
		if ($success !== 0)
		{
			die("Error occured by creating tables");
		}
		
		$con = Registry::getInstance()->getDatabase();
		$rs = new RecordSet();
		
		
		$rs->execute(
			$con->newQuery()->setQueryOnce(
				"SET FOREIGN_KEY_CHECKS = 0;"
			)
		);
		
		$rs->execute(
			$con->newQuery()->setQueryOnce(
				"INSERT INTO `objects` (`id`,`object_id`,`name`,`typ`,`dtype`) 
				VALUES (1,0,'ROOT-QUESTIONS','root_questions','root_questions');"
			)
		);
		
		$rs->execute(
			$con->newQuery()->setQueryOnce(
				"INSERT INTO `objects` (`id`,`object_id`,`name`,`typ`,`dtype`)
				VALUES (2,0,'ROOT-MESSWERTE','root_messwerte','root_messwerte');"
			)
		);
		
		$rs->execute(
			$con->newQuery()->setQueryOnce(
				"SET FOREIGN_KEY_CHECKS = 1;"
			)
		);
	}
	
	/**
	 * Erstellt die Testuser
	 * 
	 * @return void
	 */
	public function testCreateTestDataMembers()
	{
		/** user male **/
		$user = new UserModel(
			array(
				'username' => 'maxMuster',
				'firstname' => 'Max',
				'lastname' => 'Mustermann',
				'birthday' => '1980-01-01',
				'gender' => UserModel::GENDER_MALE,
				'email' => 'maxMuster@unittesting.intern'
			)		
		);
		
		$user = UserManager::insertUser($user, 'test123');
		
		if ($user)
		{
			Registry::getInstance()->userMale = $user->getId();
		}
		else
		{
			die('Error creating user male');
		}
		
		/** user female **/
		$user = new UserModel(
			array(
				'username' => 'sabMuster',
				'firstname' => 'Sabine',
				'lastname' => 'Musterfrau',
				'birthday' => '2011-01-01',
				'gender' => UserModel::GENDER_FEMALE,
				'email' => 'sabMuster@unittesting.intern'
			)
		);
		
		$user = UserManager::insertUser($user, 'test123');
		
		if ($user)
		{
			Registry::getInstance()->userFemale = $user->getId();
		}
		else
		{
			die('Error creating user female');
		}
	}
	
	/**
	 * Erstellt die Testuser
	 *
	 * @return void
	 */
	public function testCreateTestDataQuestionary()
	{
		$questionary = ObjectFactory::create(
			'questionary', 
			array(
				'name' => 'Standardfragebogen',
				'object' => ObjectFactory::getById(1)
			)	
		); 
		
		$questionary = ObjectFactory::insert($questionary);
		
		if ($questionary->getId())
		{
			Registry::getInstance()->questionary = $questionary->getId();
		}
	}
	
	/**
	 * Erstelle Testdaten für Testing von "abs"
	 * 
	 * @return void
	 */
	public function testCreateTestDataAbs()
	{
		/** Position a) **/
		$positionA = ObjectFactory::create(
			'position', 
			array(
				'name' => 'Testposition a) Function "abs"',
				'formel' => '10',
				'object' => ObjectFactory::getById(2)
			)
		);
		
		$positionA = ObjectFactory::insert($positionA);
		
		if ($positionA->getId())
		{
			Registry::getInstance()->absPositionA = $positionA->getId();
		}
		
		/** Skala für Position a) **/
		$scale = new ScaleModel();
		$scale->setTyp(ScaleModel::GENDER_MALE);
		$scale->setObject($positionA);
		
		for ($i = 0; $i <= 100; $i = $i+10)
		{
			/** 0 / 0 **/
			$scale->setValue("".($i / 5)."");
			$scale->setPercent("$i");
			ScaleManager::insert($scale);
		}
		
		$scale->setTyp(ScaleModel::GENDER_FEMALE);
		for ($i = 0; $i <= 100; $i = $i+10)
		{
			/** 0 / 0 **/
			$scale->setValue("".($i / 5)."");
			$scale->setPercent("$i");
			ScaleManager::insert($scale);
		}
		
		/** Position b) **/
		$positionB = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition b) Function "abs"',
				'formel' => 'abs[a]',
				'object' => ObjectFactory::getById(2)
			)
		);
		
		$positionB = ObjectFactory::insert($positionB);
		
		if ($positionB->getId())
		{
			Registry::getInstance()->absPositionB = $positionB->getId();
		}
		
		$vars = $positionB->getVariables();
		$vars[0]->setLink($positionA);
		
		ObjectFactory::update($vars[0]);
		
		/** Position c) **/
		$positionC = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition b) Function "abs"',
				'formel' => 'a',
				'object' => ObjectFactory::getById(2)
			)
		);
		
		$positionC = ObjectFactory::insert($positionC);
		
		if ($positionC->getId())
		{
			Registry::getInstance()->absPositionC = $positionC->getId();
		}
		
		$vars = $positionC->getVariables();
		$vars[0]->setLink($positionA);
		
		ObjectFactory::update($vars[0]);
		
	}
	
	/**
	 * Erstelle Testdaten für Testing von "alter"
	 *
	 * @return void
	 */
	public function testCreateTestDataAlter()
	{
		/** Position **/
		$position = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition Function "alter"',
				'formel' => 'alter[]',
				'object' => ObjectFactory::getById(2)
			)
		);
	
		$position = ObjectFactory::insert($position);
	
		if ($position->getId())
		{
			Registry::getInstance()->alterPosition = $position->getId();
		}
	}
	
	/**
	 * Erstelle Testdaten für Testing von "anzahl"
	 *
	 * @return void
	 */
	public function testCreateTestDataAnzahl()
	{
		/** Position a) **/
		$positionA = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition a) Function "anzahl"',
				'formel' => '0',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionA = ObjectFactory::insert($positionA);
		if ($positionA->getId())
		{
			Registry::getInstance()->anzahlPositionA = $positionA->getId();
		}
		
		/** Position b) **/
		$positionB = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition b) Function "anzahl"',
				'formel' => '1',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionB = ObjectFactory::insert($positionB);
		if ($positionB->getId())
		{
			Registry::getInstance()->anzahlPositionB = $positionB->getId();
		}
		
		/** Position c) **/
		$positionC = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition c) Function "anzahl"',
				'formel' => '1',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionC = ObjectFactory::insert($positionC);
		if ($positionC->getId())
		{
			Registry::getInstance()->anzahlPositionC = $positionC->getId();
		}
		
		/** Position d) **/
		$positionD = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition d) Function "anzahl"',
				'formel' => '0',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionD = ObjectFactory::insert($positionD);
		if ($positionD->getId())
		{
			Registry::getInstance()->anzahlPositionD = $positionD->getId();
		}
				
		/** Position e) **/
		$positionE = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition e) Function "anzahl"',
				'formel' => 'anzahl[a;b;c;d]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionE = ObjectFactory::insert($positionE);
		if ($positionE->getId())
		{
			Registry::getInstance()->anzahlPositionE = $positionE->getId();
		}
		
		$vars = $positionE->getVariables();
		$vars[0]->setLink($positionA);
		ObjectFactory::update($vars[0]);
		$vars[1]->setLink($positionB);
		ObjectFactory::update($vars[1]);
		$vars[2]->setLink($positionC);
		ObjectFactory::update($vars[2]);
		$vars[3]->setLink($positionD);
		ObjectFactory::update($vars[3]);
				
		/** Position f) **/
		$positionF = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition e) Function "anzahl"',
				'formel' => 'anzahl[a;b]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionF = ObjectFactory::insert($positionF);
		if ($positionF->getId())
		{
			Registry::getInstance()->anzahlPositionF = $positionF->getId();
		}
		
		$vars = $positionF->getVariables();
		$vars[0]->setLink($positionA);
		ObjectFactory::update($vars[0]);
		$vars[1]->setLink($positionD);
		ObjectFactory::update($vars[1]);
		
		/** Position g) **/
		$positionG = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition e) Function "anzahl"',
				'formel' => 'anzahl[a;b;c]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionG = ObjectFactory::insert($positionG);
		if ($positionG->getId())
		{
			Registry::getInstance()->anzahlPositionG = $positionG->getId();
		}
		
		$vars = $positionG->getVariables();
		$vars[0]->setLink($positionA);
		ObjectFactory::update($vars[0]);
		$vars[1]->setLink($positionB);
		ObjectFactory::update($vars[1]);
		$vars[2]->setLink($positionD);
		ObjectFactory::update($vars[2]);
		
	}
	
	/**
	 * Erstelle Testdaten für Testing von "beantwortet"
	 *
	 * @return void
	 */
	public function testCreateTestDataBeantwortet()
	{
		/** Frage a) **/
		$questionA = ObjectFactory::create(
			'question',
			array(
				'name' => 'Testfrage a) Function "beantwortet"',
				'question' => 'Testfrage a) mit Antwort Number',
				'object' => ObjectFactory::getById(Registry::getInstance()->questionary)
			)
		);
		$questionA = ObjectFactory::insert($questionA);
		if ($questionA->getId())
		{
			Registry::getInstance()->beantwortetFrageA = $questionA->getId();
		}
		
		/** Antwort Number **/
		$questionANumber = ObjectFactory::create(
			'number',
			array(
				'name' => 'Testantwort für a) (Number) Function "beantwortet"',
				'object' => $questionA
			)
		);
		$questionANumber = ObjectFactory::insert($questionANumber);
		if ($questionANumber->getId())
		{
			Registry::getInstance()->beantwortetFrageAAntwortNumber = $questionANumber->getId();
		}
		
		/** Antwort Text **/
		$questionAText = ObjectFactory::create(
			'text',
			array(
				'name' => 'Testantwort für a) (Text) Function "beantwortet"',
				'object' => $questionA
			)
		);
		$questionAText = ObjectFactory::insert($questionAText);
		if ($questionAText->getId())
		{
			Registry::getInstance()->beantwortetFrageAAntwortText = $questionAText->getId();
		}
		
		/** Antwort Checkbox **/
		$questionACheckbox = ObjectFactory::create(
			'checkbox',
			array(
				'name' => 'Testantwort für a) (Checkbox) Function "beantwortet"',
				'object' => $questionA,
				'value' => 10
			)
		);
		$questionACheckbox = ObjectFactory::insert($questionACheckbox);
		if ($questionACheckbox->getId())
		{
			Registry::getInstance()->beantwortetFrageAAntwortCheckbox = $questionACheckbox->getId();
		}
		
		/** Antwort Radio **/
		$questionARadio = ObjectFactory::create(
			'radio',
			array(
				'name' => 'Testantwort für a) (Radio) Function "beantwortet"',
				'object' => $questionA,
			)
		);
		$questionARadio = ObjectFactory::insert($questionARadio);
		if ($questionARadio->getId())
		{
			Registry::getInstance()->beantwortetFrageAAntwortRadio = $questionARadio->getId();
		}
		
		/** Antwort Radio-Option1 **/
		$questionAOption1 = ObjectFactory::create(
			'option',
			array(
				'name' => 'Testantwort für a) (Radio-Option1) Function "beantwortet"',
				'object' => $questionARadio,
				'value' => '0'
			)
		);
		$questionAOption1 = ObjectFactory::insert($questionAOption1);
		if ($questionAOption1->getId())
		{
			Registry::getInstance()->beantwortetFrageAAntwortRadioOpt1 = $questionAOption1->getId();
		}
		
		/** Antwort Radio-Option2 **/
		$questionAOption2 = ObjectFactory::create(
			'option',
			array(
				'name' => 'Testantwort für a) (Radio-Option2) Function "beantwortet"',
				'object' => $questionARadio,
				'value' => 10
			)
		);
		$questionAOption2 = ObjectFactory::insert($questionAOption2);
		if ($questionAOption2->getId())
		{
			Registry::getInstance()->beantwortetFrageAAntwortRadioOpt2 = $questionAOption2->getId();
		}
		
		
		/** Frage b) **/
		$questionB = ObjectFactory::create(
			'question',
			array(
				'name' => 'Testfrage b) Function "beantwortet"',
				'question' => 'Testfrage b) mit Antwort Number',
				'object' => ObjectFactory::getById(Registry::getInstance()->questionary)
			)
		);
		$questionB = ObjectFactory::insert($questionB);
		if ($questionB->getId())
		{
			Registry::getInstance()->beantwortetFrageB = $questionB->getId();
		}
		
		/** Antwort Number **/
		$questionBNumber = ObjectFactory::create(
			'number',
			array(
				'name' => 'Testantwort für b) (Number) Function "beantwortet"',
				'object' => $questionB
			)
		);
		$questionBNumber = ObjectFactory::insert($questionBNumber);
		if ($questionBNumber->getId())
		{
			Registry::getInstance()->beantwortetFrageBAntwortNumber = $questionBNumber->getId();
		}
		
		/** Antwort Text **/
		$questionBText = ObjectFactory::create(
			'text',
			array(
				'name' => 'Testantwort für b) (Text) Function "beantwortet"',
				'object' => $questionB
			)
		);
		$questionBText = ObjectFactory::insert($questionBText);
		if ($questionBText->getId())
		{
			Registry::getInstance()->beantwortetFrageBAntwortText = $questionBText->getId();
		}
		
		/** Antwort Checkbox **/
		$questionBCheckbox = ObjectFactory::create(
			'checkbox',
			array(
				'name' => 'Testantwort für b) (Checkbox) Function "beantwortet"',
				'object' => $questionB,
				'value' => 10
			)
		);
		$questionBCheckbox = ObjectFactory::insert($questionBCheckbox);
		if ($questionBCheckbox->getId())
		{
			Registry::getInstance()->beantwortetFrageBAntwortCheckbox = $questionBCheckbox->getId();
		}
		
		/** Antwort Radio **/
		$questionBRadio = ObjectFactory::create(
			'radio',
			array(
				'name' => 'Testantwort für b) (Radio) Function "beantwortet"',
				'object' => $questionB,
			)
		);
		$questionBRadio = ObjectFactory::insert($questionBRadio);
		if ($questionBRadio->getId())
		{
			Registry::getInstance()->beantwortetFrageBAntwortRadio = $questionBRadio->getId();
		}
		
		/** Antwort Radio-Option1 **/
		$questionBOption1 = ObjectFactory::create(
			'option',
			array(
				'name' => 'Testantwort für b) (Radio-Option1) Function "beantwortet"',
				'object' => $questionBRadio,
				'value' => '0'
			)
		);
		$questionBOption1 = ObjectFactory::insert($questionBOption1);
		if ($questionBOption1->getId())
		{
			Registry::getInstance()->beantwortetFrageBAntwortRadioOpt1 = $questionBOption1->getId();
		}
		
		/** Antwort Radio-Option2 **/
		$questionBOption2 = ObjectFactory::create(
			'option',
			array(
				'name' => 'Testantwort für b) (Radio-Option2) Function "beantwortet"',
				'object' => $questionBRadio,
				'value' => 10
			)
		);
		$questionBOption2 = ObjectFactory::insert($questionBOption2);
		if ($questionBOption2->getId())
		{
			Registry::getInstance()->beantwortetFrageBAntwortRadioOpt2 = $questionBOption2->getId();
		}
		
		/** Frage c) **/
		$questionC = ObjectFactory::create(
			'question',
			array(
				'name' => 'Testfrage c) Function "beantwortet"',
				'question' => 'Testfrage c) mit Antwort Number',
				'object' => ObjectFactory::getById(Registry::getInstance()->questionary)
			)
		);
		$questionC = ObjectFactory::insert($questionC);
		if ($questionC->getId())
		{
			Registry::getInstance()->beantwortetFrageC = $questionC->getId();
		}
		
		/** Antwort Number **/
		$questionCNumber = ObjectFactory::create(
			'number',
			array(
				'name' => 'Testantwort für c) (Number) Function "beantwortet"',
				'object' => $questionC
			)
		);
		$questionCNumber = ObjectFactory::insert($questionCNumber);
		if ($questionCNumber->getId())
		{
			Registry::getInstance()->beantwortetFrageCAntwortNumber = $questionCNumber->getId();
		}
		
		/** Antwort Text **/
		$questionCText = ObjectFactory::create(
			'text',
			array(
				'name' => 'Testantwort für c) (Text) Function "beantwortet"',
				'object' => $questionC
			)
		);
		$questionCText = ObjectFactory::insert($questionCText);
		if ($questionCText->getId())
		{
			Registry::getInstance()->beantwortetFrageCAntwortText = $questionCText->getId();
		}
		
		/** Antwort Checkbox **/
		$questionCCheckbox = ObjectFactory::create(
			'checkbox',
			array(
				'name' => 'Testantwort für c) (Checkbox) Function "beantwortet"',
				'object' => $questionC,
				'value' => 10
			)
		);
		$questionCCheckbox = ObjectFactory::insert($questionCCheckbox);
		if ($questionCCheckbox->getId())
		{
			Registry::getInstance()->beantwortetFrageCAntwortCheckbox = $questionCCheckbox->getId();
		}
		
		/** Antwort Radio **/
		$questionCRadio = ObjectFactory::create(
			'radio',
			array(
				'name' => 'Testantwort für c) (Radio) Function "beantwortet"',
				'object' => $questionC,
			)
		);
		$questionCRadio = ObjectFactory::insert($questionCRadio);
		if ($questionCRadio->getId())
		{
			Registry::getInstance()->beantwortetFrageCAntwortRadio = $questionCRadio->getId();
		}
		
		/** Antwort Radio-Option1 **/
		$questionCOption1 = ObjectFactory::create(
			'option',
			array(
				'name' => 'Testantwort für c) (Radio-Option1) Function "beantwortet"',
				'object' => $questionCRadio,
				'value' => '0'
			)
		);
		$questionCOption1 = ObjectFactory::insert($questionCOption1);
		if ($questionCOption1->getId())
		{
			Registry::getInstance()->beantwortetFrageCAntwortRadioOpt1 = $questionCOption1->getId();
		}
		
		/** Antwort Radio-Option2 **/
		$questionCOption2 = ObjectFactory::create(
			'option',
			array(
				'name' => 'Testantwort für c) (Radio-Option2) Function "beantwortet"',
				'object' => $questionCRadio,
				'value' => 10
			)
		);
		$questionCOption2 = ObjectFactory::insert($questionCOption2);
		if ($questionCOption2->getId())
		{
			Registry::getInstance()->beantwortetFrageCAntwortRadioOpt2 = $questionCOption2->getId();
		}
		
		
		/** Position d) **/
		$positionD = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition d) Function "beantwortet"',
				'formel' => 'beantwortet[a]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionD = ObjectFactory::insert($positionD);
		if ($positionD->getId())
		{
			Registry::getInstance()->beantwortetPositionD = $positionD->getId();
		}
		
		$vars = $positionD->getVariables();
		$vars[0]->setLink($questionA);
		ObjectFactory::update($vars[0]);
		
		/** Position e) **/
		$positionE = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition e) Function "beantwortet"',
				'formel' => 'beantwortet[a]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionE = ObjectFactory::insert($positionE);
		if ($positionE->getId())
		{
			Registry::getInstance()->beantwortetPositionE = $positionE->getId();
		}
		
		$vars = $positionE->getVariables();
		$vars[0]->setLink($questionB);
		ObjectFactory::update($vars[0]);
		
		/** Position f) **/
		$positionF = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition f) Function "beantwortet"',
				'formel' => 'beantwortet[a]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionF = ObjectFactory::insert($positionF);
		if ($positionF->getId())
		{
			Registry::getInstance()->beantwortetPositionF = $positionF->getId();
		}
		
		$vars = $positionF->getVariables();
		$vars[0]->setLink($questionC);
		ObjectFactory::update($vars[0]);
		
		/** Position g) **/
		$positionG = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition g) Function "beantwortet"',
				'formel' => 'beantwortet[a] + beantwortet[b] + beantwortet[c]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionG = ObjectFactory::insert($positionG);
		if ($positionG->getId())
		{
			Registry::getInstance()->beantwortetPositionG = $positionG->getId();
		}
		
		$vars = $positionG->getVariables();
		$vars[0]->setLink($questionA);
		ObjectFactory::update($vars[0]);
		$vars[1]->setLink($questionB);
		ObjectFactory::update($vars[1]);
		$vars[2]->setLink($questionC);
		ObjectFactory::update($vars[2]);
		
	}
	
	/**
	 * Erstelle Testdaten für Testing von "exponent"
	 *
	 * @return void
	 */
	public function testCreateTestDataExponent()
	{
		/** Position a) **/
		$positionA = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition a) Function "exponent"',
				'formel' => 'exponent[0]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionA = ObjectFactory::insert($positionA);
		if ($positionA->getId())
		{
			Registry::getInstance()->exponentPositionA = $positionA->getId();
		}
		
		/** Position b) **/
		$positionB = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition b) Function "exponent"',
				'formel' => 'exponent[1]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionB = ObjectFactory::insert($positionB);
		if ($positionB->getId())
		{
			Registry::getInstance()->exponentPositionB = $positionB->getId();
		}
		
		/** Position c) **/
		$positionC = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition c) Function "exponent"',
				'formel' => 'exponent[10]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionC = ObjectFactory::insert($positionC);
		if ($positionC->getId())
		{
			Registry::getInstance()->exponentPositionC = $positionC->getId();
		}
	}
	
	/**
	 * Erstelle Testdaten für Testing von "geschlecht"
	 *
	 * @return void
	 */
	public function testCreateTestDataGeschlecht()
	{
		/** Position a) **/
		$positionA = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition a) Function "geschlecht"',
				'formel' => 'geschlecht[]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionA = ObjectFactory::insert($positionA);
		if ($positionA->getId())
		{
			Registry::getInstance()->geschlechtPositionA = $positionA->getId();
		}
	}
	
	/**
	 * Erstelle Testdaten für Testing von "halbwertszeit"
	 *
	 * @return void
	 */
	public function testCreateTestDataHalbwertszeit()
	{
		/** Frage a) **/
		$questionA = ObjectFactory::create(
			'question',
			array(
				'name' => 'Testfrage a) Function "halbwertszeit"',
				'question' => 'Testfrage a) mit Antwort Number',
				'object' => ObjectFactory::getById(Registry::getInstance()->questionary)
			)
		);
		$questionA = ObjectFactory::insert($questionA);
		if ($questionA->getId())
		{
			Registry::getInstance()->halbwertszeitFrageA = $questionA->getId();
		}
		
		/** Antwort Number **/
		$questionANumber = ObjectFactory::create(
			'number',
			array(
				'name' => 'Testantwort für a) (Number) Function "halbwertszeit"',
				'object' => $questionA
			)
		);
		$questionANumber = ObjectFactory::insert($questionANumber);
		if ($questionANumber->getId())
		{
			Registry::getInstance()->halbwertszeitFrageAAntwortNumber = $questionANumber->getId();
		}
		
		/** Halbwertszeit Number **/
		$questionAHalflife = ObjectFactory::create(
			'halflife',
			array(
				'name' => 'Halbwertszeit für a) Function "halbwertszeit"',
				'object' => $questionA,
				'value' => 1
			)
		);
		$questionAHalflife = ObjectFactory::insert($questionAHalflife);
		if ($questionAHalflife->getId())
		{
			Registry::getInstance()->halbwertszeitFrageAHalbwertszeit = $questionAHalflife->getId();
		}
		
		/** Position b) **/
		$positionB = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition b) Function "halbwertszeit"',
				'formel' => 'halbwertszeit[a]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionB = ObjectFactory::insert($positionB);
		if ($positionB->getId())
		{
			Registry::getInstance()->halbwertszeitPositionB = $positionB->getId();
		}
		
		$vars = $positionB->getVariables();
		$vars[0]->setLink($questionA);
		ObjectFactory::update($vars[0]);
		
	}
	
	/**
	 * Erstelle Testdaten für Testing von "min"
	 *
	 * @return void
	 */
	public function testCreateTestDataMin()
	{
		/** Position a) **/
		$positionA = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition a) Function "min"',
				'formel' => 'min[2;3;6;1;10]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionA = ObjectFactory::insert($positionA);
		if ($positionA->getId())
		{
			Registry::getInstance()->minPositionA = $positionA->getId();
		}
	
		/** Position b) **/
		$positionB = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition b) Function "min"',
				'formel' => 'min[2;3;6;10]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionB = ObjectFactory::insert($positionB);
		if ($positionB->getId())
		{
			Registry::getInstance()->minPositionB = $positionB->getId();
		}
	
		/** Position c) **/
		$positionC = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition c) Function "min"',
				'formel' => 'min[111;11;1;1111]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionC = ObjectFactory::insert($positionC);
		if ($positionC->getId())
		{
			Registry::getInstance()->minPositionC = $positionC->getId();
		}
	}
	
	/**
	 * Erstelle Testdaten für Testing von "max"
	 *
	 * @return void
	 */
	public function testCreateTestDataMax()
	{
		/** Position a) **/
		$positionA = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition a) Function "max"',
				'formel' => 'max[2;3;6;1;10]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionA = ObjectFactory::insert($positionA);
		if ($positionA->getId())
		{
			Registry::getInstance()->maxPositionA = $positionA->getId();
		}
	
		/** Position b) **/
		$positionB = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition b) Function "max"',
				'formel' => 'max[2;3;6;10]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionB = ObjectFactory::insert($positionB);
		if ($positionB->getId())
		{
			Registry::getInstance()->maxPositionB = $positionB->getId();
		}
	
		/** Position c) **/
		$positionC = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition c) Function "max"',
				'formel' => 'max[111;11;1;1111]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionC = ObjectFactory::insert($positionC);
		if ($positionC->getId())
		{
			Registry::getInstance()->maxPositionC = $positionC->getId();
		}
	}
	
	/**
	 * Erstelle Testdaten für Testing von "negativfrage"
	 *
	 * @return void
	 */
	public function testCreateTestDataNegativfrage()
	{
		/** Frage a) **/
		$questionA = ObjectFactory::create(
			'question',
			array(
				'name' => 'Testfrage a) Function "negativfrage"',
				'question' => 'Testfrage a) mit Antwort Number',
				'object' => ObjectFactory::getById(Registry::getInstance()->questionary)
			)
		);
		$questionA = ObjectFactory::insert($questionA);
		if ($questionA->getId())
		{
			Registry::getInstance()->negativfrageFrageA = $questionA->getId();
		}
	
		/** Antwort a) Number **/
		$questionANumber = ObjectFactory::create(
			'number',
			array(
				'name' => 'Testantwort für a) (Number) Function "negativfrage"',
				'object' => $questionA
			)
		);
		$questionANumber = ObjectFactory::insert($questionANumber);
		if ($questionANumber->getId())
		{
			Registry::getInstance()->negativfrageFrageAAntwortNumber = $questionANumber->getId();
		}
	
		
		/** Frage b) **/
		$questionB = ObjectFactory::create(
			'question',
			array(
				'name' => 'Testfrage b) Function "negativfrage"',
				'question' => 'Testfrage b) mit Antwort Text',
				'object' => ObjectFactory::getById(Registry::getInstance()->questionary)
			)
		);
		$questionB = ObjectFactory::insert($questionB);
		if ($questionB->getId())
		{
			Registry::getInstance()->negativfrageFrageB = $questionB->getId();
		}
		/** Antwort b) Text **/
		$questionBText = ObjectFactory::create(
			'text',
			array(
				'name' => 'Testantwort für b) (Text) Function "negativfrage"',
				'object' => $questionB
			)
		);
		$questionBText = ObjectFactory::insert($questionBText);
		if ($questionBText->getId())
		{
			Registry::getInstance()->negativfrageFrageBAntwortText = $questionBText->getId();
		}
	
		
		/** Frage c) **/
		$questionC = ObjectFactory::create(
			'question',
			array(
				'name' => 'Testfrage c) Function "negativfrage"',
				'question' => 'Testfrage c) mit Antwort Checkbox',
				'object' => ObjectFactory::getById(Registry::getInstance()->questionary)
			)
		);
		$questionC = ObjectFactory::insert($questionC);
		if ($questionC->getId())
		{
			Registry::getInstance()->negativfrageFrageC = $questionC->getId();
		}
		/** Antwort c) Checkbox **/
		$questionCCheckbox = ObjectFactory::create(
			'checkbox',
			array(
				'name' => 'Testantwort für c) (Checkbox) Function "negativfrage"',
				'object' => $questionC,
				'value' => 10
			)
		);
		$questionCCheckbox = ObjectFactory::insert($questionCCheckbox);
		if ($questionCCheckbox->getId())
		{
			Registry::getInstance()->negativfrageFrageCAntwortCheckbox = $questionCCheckbox->getId();
		}
	
		/** Frage d) **/
		$questionD = ObjectFactory::create(
			'question',
			array(
				'name' => 'Testfrage d) Function "negativfrage"',
				'question' => 'Testfrage d) mit Antwort Radio',
				'object' => ObjectFactory::getById(Registry::getInstance()->questionary)
			)
		);
		$questionD = ObjectFactory::insert($questionD);
		if ($questionD->getId())
		{
			Registry::getInstance()->negativfrageFrageD = $questionD->getId();
		}
		/** Antwort Radio **/
		$questionDRadio = ObjectFactory::create(
			'radio',
			array(
				'name' => 'Testantwort für d) (Radio) Function "negativfrage"',
				'object' => $questionD,
			)
		);
		$questionDRadio = ObjectFactory::insert($questionDRadio);
		if ($questionDRadio->getId())
		{
			Registry::getInstance()->negativfrageFrageDAntwortRadio = $questionDRadio->getId();
		}
	
		/** Antwort Radio-Option1 **/
		$questionDOption1 = ObjectFactory::create(
			'option',
			array(
				'name' => 'Testantwort für d) (Radio-Option1) Function "negativfrage"',
				'object' => $questionDRadio,
				'value' => '0'
			)
		);
		$questionDOption1 = ObjectFactory::insert($questionDOption1);
		if ($questionDOption1->getId())
		{
			Registry::getInstance()->negativfrageFrageAAntwortRadioOpt1 = $questionDOption1->getId();
		}
	
		/** Antwort Radio-Option2 **/
		$questionDOption2 = ObjectFactory::create(
			'option',
			array(
				'name' => 'Testantwort für d) (Radio-Option2) Function "negativfrage"',
				'object' => $questionDRadio,
				'value' => 10
			)
		);
		$questionDOption2 = ObjectFactory::insert($questionDOption2);
		if ($questionDOption2->getId())
		{
			Registry::getInstance()->negativfrageFrageDAntwortRadioOpt2 = $questionDOption2->getId();
		}
		
		
		
		/** Position e) **/
		$positionE = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition e) Function "negativfrage"',
				'formel' => 'negativfrage[a]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionE = ObjectFactory::insert($positionE);
		if ($positionE->getId())
		{
			Registry::getInstance()->negativfragePositionE = $positionE->getId();
		}
		$vars = $positionE->getVariables();
		$vars[0]->setLink($questionANumber);
		ObjectFactory::update($vars[0]);
		
		/** Position f) **/
		$positionF = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition f) Function "negativfrage"',
				'formel' => 'negativfrage[a]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionF = ObjectFactory::insert($positionF);
		if ($positionF->getId())
		{
			Registry::getInstance()->negativfragePositionF = $positionF->getId();
		}
		$vars = $positionF->getVariables();
		$vars[0]->setLink($questionBText);
		ObjectFactory::update($vars[0]);
		
		/** Position G) **/
		$positionG = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition g) Function "negativfrage"',
				'formel' => 'negativfrage[a]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionG = ObjectFactory::insert($positionG);
		if ($positionG->getId())
		{
			Registry::getInstance()->negativfragePositionG = $positionG->getId();
		}
		$vars = $positionG->getVariables();
		$vars[0]->setLink($questionCCheckbox);
		ObjectFactory::update($vars[0]);
		
		/** Position H) **/
		$positionH = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition h) Function "negativfrage"',
				'formel' => 'negativfrage[a]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionH = ObjectFactory::insert($positionH);
		if ($positionH->getId())
		{
			Registry::getInstance()->negativfragePositionH = $positionH->getId();
		}
		$vars = $positionH->getVariables();
		$vars[0]->setLink($questionD);
		ObjectFactory::update($vars[0]);
		
	}
	
	/**
	 * Erstelle Testdaten für Testing von "nicht"
	 *
	 * @return void
	 */
	public function testCreateTestDataNicht()
	{
		/** Position a) **/
		$positionA = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition a) Function "nicht"',
				'formel' => 'nicht[1=0]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionA = ObjectFactory::insert($positionA);
		if ($positionA->getId())
		{
			Registry::getInstance()->nichtPositionA = $positionA->getId();
		}
	
		/** Position b) **/
		$positionB = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition b) Function "nicht"',
				'formel' => 'nicht[1=1]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionB = ObjectFactory::insert($positionB);
		if ($positionB->getId())
		{
			Registry::getInstance()->nichtPositionB = $positionB->getId();
		}
	
		/** Position c) **/
		$positionC = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition c) Function "nicht"',
				'formel' => 'nicht[1>0]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionC = ObjectFactory::insert($positionC);
		if ($positionC->getId())
		{
			Registry::getInstance()->nichtPositionC = $positionC->getId();
		}
	}
	
	/**
	 * Erstelle Testdaten für Testing von "quadrat"
	 *
	 * @return void
	 */
	public function testCreateTestDataQuadrat()
	{
		/** Position a) **/
		$positionA = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition a) Function "quadrat"',
				'formel' => 'quadrat[2]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionA = ObjectFactory::insert($positionA);
		if ($positionA->getId())
		{
			Registry::getInstance()->quadratPositionA = $positionA->getId();
		}
	
		/** Position b) **/
		$positionB = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition b) Function "quadrat"',
				'formel' => 'quadrat[-1]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionB = ObjectFactory::insert($positionB);
		if ($positionB->getId())
		{
			Registry::getInstance()->quadratPositionB = $positionB->getId();
		}
	
		/** Position c) **/
		$positionC = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition c) Function "quadrat"',
				'formel' => 'quadrat[0] + quadrat[4]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionC = ObjectFactory::insert($positionC);
		if ($positionC->getId())
		{
			Registry::getInstance()->quadratPositionC = $positionC->getId();
		}
	}
	
	/**
	 * Erstelle Testdaten für Testing von "summe"
	 *
	 * @return void
	 */
	public function testCreateTestDataSumme()
	{
		/** Position a) **/
		$positionA = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition a) Function "summe"',
				'formel' => '-1',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionA = ObjectFactory::insert($positionA);
		if ($positionA->getId())
		{
			Registry::getInstance()->summePositionA = $positionA->getId();
		}
	
		/** Position b) **/
		$positionB = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition b) Function "summe"',
				'formel' => '4',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionB = ObjectFactory::insert($positionB);
		if ($positionB->getId())
		{
			Registry::getInstance()->summePositionB = $positionB->getId();
		}
	
		/** Position c) **/
		$positionC = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition c) Function "summe"',
				'formel' => 'summe[a;b]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionC = ObjectFactory::insert($positionC);
		if ($positionC->getId())
		{
			Registry::getInstance()->summePositionC = $positionC->getId();
		}
		$vars = $positionC->getVariables();
		$vars[0]->setLink($positionA);
		$vars[1]->setLink($positionB);
		ObjectFactory::update($vars[0]);
		ObjectFactory::update($vars[1]);
		
	}
	
	/**
	 * Erstelle Testdaten für Testing von "wenn"
	 *
	 * @return void
	 */
	public function testCreateTestDataWenn()
	{
		/** Position a) **/
		$positionA = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition a) Function "wenn"',
				'formel' => '1',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionA = ObjectFactory::insert($positionA);
		if ($positionA->getId())
		{
			Registry::getInstance()->wennPositionA = $positionA->getId();
		}
	
		/** Position b) **/
		$positionB = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition b) Function "wenn"',
				'formel' => '0',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionB = ObjectFactory::insert($positionB);
		if ($positionB->getId())
		{
			Registry::getInstance()->wennPositionB = $positionB->getId();
		}
		
		/** Position c) **/
		$positionC = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition c) Function "wenn"',
				'formel' => '5',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionC = ObjectFactory::insert($positionC);
		if ($positionC->getId())
		{
			Registry::getInstance()->wennPositionC = $positionC->getId();
		}
	
		/** Position d) **/
		$positionD = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition d) Function "wenn"',
				'formel' => 'wenn[a>0;c;b]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionD = ObjectFactory::insert($positionD);
		if ($positionD->getId())
		{
			Registry::getInstance()->wennPositionD = $positionD->getId();
		}
		$vars = $positionD->getVariables();
		$vars[0]->setLink($positionA);
		$vars[1]->setLink($positionB);
		$vars[2]->setLink($positionC);
		ObjectFactory::update($vars[0]);
		ObjectFactory::update($vars[1]);
		ObjectFactory::update($vars[2]);
		
		/** Position e) **/
		$positionE = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition e) Function "wenn"',
				'formel' => 'wenn[a=b;c;b]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionE = ObjectFactory::insert($positionE);
		if ($positionE->getId())
		{
			Registry::getInstance()->wennPositionE = $positionE->getId();
		}
		$vars = $positionE->getVariables();
		$vars[0]->setLink($positionA);
		$vars[1]->setLink($positionB);
		$vars[2]->setLink($positionC);
		ObjectFactory::update($vars[0]);
		ObjectFactory::update($vars[1]);
		ObjectFactory::update($vars[2]);
		
		
		/** Position f) **/
		$positionF = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition f) Function "wenn"',
				'formel' => 'wenn[c>a;c;a]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionF = ObjectFactory::insert($positionF);
		if ($positionF->getId())
		{
			Registry::getInstance()->wennPositionF = $positionF->getId();
		}
		$vars = $positionF->getVariables();
		$vars[0]->setLink($positionA);
		$vars[1]->setLink($positionC);
		ObjectFactory::update($vars[0]);
		ObjectFactory::update($vars[1]);
	
	}
	
	/**
	 * Erstelle Testdaten für Testing von "wert"
	 *
	 * @return void
	 */
	public function testCreateTestDataWert()
	{
		/** Frage a) **/
		$questionA = ObjectFactory::create(
			'question',
			array(
				'name' => 'Testfrage a) Function "wert"',
				'question' => 'Testfrage a) mit Antwort Number',
				'object' => ObjectFactory::getById(Registry::getInstance()->questionary)
			)
		);
		$questionA = ObjectFactory::insert($questionA);
		if ($questionA->getId())
		{
			Registry::getInstance()->wertFrageA = $questionA->getId();
		}
	
		/** Antwort a) Number **/
		$questionANumber = ObjectFactory::create(
			'number',
			array(
				'name' => 'Testantwort für a) (Number) Function "wert"',
				'object' => $questionA
			)
		);
		$questionANumber = ObjectFactory::insert($questionANumber);
		if ($questionANumber->getId())
		{
			Registry::getInstance()->wertFrageAAntwortNumber = $questionANumber->getId();
		}
	
	
		/** Frage b) **/
		$questionB = ObjectFactory::create(
			'question',
			array(
				'name' => 'Testfrage b) Function "wert"',
				'question' => 'Testfrage b) mit Antwort Text',
				'object' => ObjectFactory::getById(Registry::getInstance()->questionary)
			)
		);
		$questionB = ObjectFactory::insert($questionB);
		if ($questionB->getId())
		{
			Registry::getInstance()->wertFrageB = $questionB->getId();
		}
		/** Antwort b) Text **/
		$questionBText = ObjectFactory::create(
			'text',
			array(
				'name' => 'Testantwort für b) (Text) Function "wert"',
				'object' => $questionB
			)
		);
		$questionBText = ObjectFactory::insert($questionBText);
		if ($questionBText->getId())
		{
			Registry::getInstance()->wertFrageBAntwortText = $questionBText->getId();
		}
		
		/** Frage c) **/
		$questionC = ObjectFactory::create(
			'question',
			array(
				'name' => 'Testfrage c) Function "wert"',
				'question' => 'Testfrage c) mit Antwort Checkbox',
				'object' => ObjectFactory::getById(Registry::getInstance()->questionary)
			)
		);
		$questionC = ObjectFactory::insert($questionC);
		if ($questionC->getId())
		{
			Registry::getInstance()->wertFrageC = $questionC->getId();
		}
		/** Antwort c) Checkbox **/
		$questionCCheckbox = ObjectFactory::create(
			'checkbox',
			array(
				'name' => 'Testantwort für c) (Checkbox) Function "wert"',
				'object' => $questionC,
				'value' => 10
			)
		);
		$questionCCheckbox = ObjectFactory::insert($questionCCheckbox);
		if ($questionCCheckbox->getId())
		{
			Registry::getInstance()->wertFrageCAntwortCheckbox = $questionCCheckbox->getId();
		}
	
		/** Frage d) **/
		$questionD = ObjectFactory::create(
			'question',
			array(
				'name' => 'Testfrage d) Function "wert"',
				'question' => 'Testfrage d) mit Antwort Radio',
				'object' => ObjectFactory::getById(Registry::getInstance()->questionary)
			)
		);
		$questionD = ObjectFactory::insert($questionD);
		if ($questionD->getId())
		{
			Registry::getInstance()->wertFrageD = $questionD->getId();
		}
		/** Antwort Radio **/
		$questionDRadio = ObjectFactory::create(
			'radio',
			array(
				'name' => 'Testantwort für d) (Radio) Function "wert"',
				'object' => $questionD,
			)
		);
		$questionDRadio = ObjectFactory::insert($questionDRadio);
		if ($questionDRadio->getId())
		{
			Registry::getInstance()->wertFrageDAntwortRadio = $questionDRadio->getId();
		}
	
		/** Antwort Radio-Option1 **/
		$questionDOption1 = ObjectFactory::create(
			'option',
			array(
				'name' => 'Testantwort für d) (Radio-Option1) Function "wert"',
				'object' => $questionDRadio,
				'value' => '0'
			)
		);
		$questionDOption1 = ObjectFactory::insert($questionDOption1);
		if ($questionDOption1->getId())
		{
			Registry::getInstance()->wertFrageAAntwortRadioOpt1 = $questionDOption1->getId();
		}
	
		/** Antwort Radio-Option2 **/
		$questionDOption2 = ObjectFactory::create(
			'option',
			array(
				'name' => 'Testantwort für d) (Radio-Option2) Function "wert"',
				'object' => $questionDRadio,
				'value' => 10
			)
		);
		$questionDOption2 = ObjectFactory::insert($questionDOption2);
		if ($questionDOption2->getId())
		{
			Registry::getInstance()->wertFrageDAntwortRadioOpt2 = $questionDOption2->getId();
		}
	
		/** Position e) **/
		$positionE = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition e) Function "wert"',
				'formel' => 'wert[a]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionE = ObjectFactory::insert($positionE);
		if ($positionE->getId())
		{
			Registry::getInstance()->wertPositionE = $positionE->getId();
		}
		$vars = $positionE->getVariables();
		$vars[0]->setLink($questionANumber);
		ObjectFactory::update($vars[0]);
	
		/** Position f) **/
		$positionF = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition f) Function "wert"',
				'formel' => 'wert[a]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionF = ObjectFactory::insert($positionF);
		if ($positionF->getId())
		{
			Registry::getInstance()->wertPositionF = $positionF->getId();
		}
		$vars = $positionF->getVariables();
		$vars[0]->setLink($questionBText);
		ObjectFactory::update($vars[0]);
	
		/** Position G) **/
		$positionG = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition g) Function "wert"',
				'formel' => 'wert[a]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionG = ObjectFactory::insert($positionG);
		if ($positionG->getId())
		{
			Registry::getInstance()->wertPositionG = $positionG->getId();
		}
		$vars = $positionG->getVariables();
		$vars[0]->setLink($questionCCheckbox);
		ObjectFactory::update($vars[0]);
	
		/** Position h) **/
		$positionH = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition h) Function "wert"',
				'formel' => 'wert[a]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionH = ObjectFactory::insert($positionH);
		if ($positionH->getId())
		{
			Registry::getInstance()->wertPositionH = $positionH->getId();
		}
		$vars = $positionH->getVariables();
		$vars[0]->setLink($questionDOption1);
		ObjectFactory::update($vars[0]);
		
		/** Position i) **/
		$positionI = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition i) Function "wert"',
				'formel' => 'wert[a]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionI = ObjectFactory::insert($positionI);
		if ($positionI->getId())
		{
			Registry::getInstance()->wertPositionI = $positionI->getId();
		}
		$vars = $positionI->getVariables();
		$vars[0]->setLink($questionDOption2);
		ObjectFactory::update($vars[0]);
	
	}
	
	/**
	 * Erstelle Testdaten für Testing von "wurzel"
	 *
	 * @return void
	 */
	public function testCreateTestDataWurzel()
	{
		/** Position a) **/
		$positionA = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition a) Function "wurzel"',
				'formel' => 'wurzel[4]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionA = ObjectFactory::insert($positionA);
		if ($positionA->getId())
		{
			Registry::getInstance()->wurzelPositionA = $positionA->getId();
		}
	
		/** Position b) **/
		$positionB = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition b) Function "wurzel"',
				'formel' => 'wurzel[16]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionB = ObjectFactory::insert($positionB);
		if ($positionB->getId())
		{
			Registry::getInstance()->wurzelPositionB = $positionB->getId();
		}
	
		/** Position c) **/
		$positionC = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition c) Function "wurzel"',
				'formel' => 'wurzel[36]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionC = ObjectFactory::insert($positionC);
		if ($positionC->getId())
		{
			Registry::getInstance()->wurzelPositionC = $positionC->getId();
		}
	}
	
	/**
	 * Erstelle Testdaten für Testing von "zindex"
	 *
	 * @return void
	 */
	public function testCreateTestDataZindex()
	{
		/** Position a) **/
		$positionA = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition a) Function "zindex"',
				'formel' => '100',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionA = ObjectFactory::insert($positionA);
		if ($positionA->getId())
		{
			Registry::getInstance()->zindexPositionA = $positionA->getId();
		}
		
		/** Zindex a) **/
		$zindexA = ObjectFactory::create(
			'zindex',
			array(
				'name' => 'Testzindex a) Function "zindex"',
				'formel' => '1 + 2',
				'maxvalue' => 6,
				'object' => $positionA
			)
		);
		$zindexA = ObjectFactory::insert($zindexA);
		if ($zindexA->getId())
		{
			Registry::getInstance()->zindexZindexA = $zindexA->getId();
		}
		
		/** Position b) **/
		$positionB = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition b) Function "zindex"',
				'formel' => '100',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionB = ObjectFactory::insert($positionB);
		if ($positionB->getId())
		{
			Registry::getInstance()->zindexPositionB = $positionB->getId();
		}
		
		/** Zindex b) **/
		$zindexB = ObjectFactory::create(
			'zindex',
			array(
				'name' => 'Testzindex b) Function "zindex"',
				'formel' => '2 * 3',
				'maxvalue' => 6,
				'object' => $positionB
			)
		);
		$zindexB = ObjectFactory::insert($zindexB);
		if ($zindexB->getId())
		{
			Registry::getInstance()->zindexZindexB = $zindexB->getId();
		}
		
		/** Position c) **/
		$positionC = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition c) Function "zindex"',
				'formel' => '100',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionC = ObjectFactory::insert($positionC);
		if ($positionC->getId())
		{
			Registry::getInstance()->zindexPositionC = $positionC->getId();
		}
		
		/** Zindex c) **/
		$zindexC = ObjectFactory::create(
			'zindex',
			array(
				'name' => 'Testzindex c) Function "zindex"',
				'formel' => '2 - 3',
				'maxvalue' => 6,
				'object' => $positionC
			)
		);
		$zindexC = ObjectFactory::insert($zindexC);
		if ($zindexC->getId())
		{
			Registry::getInstance()->zindexZindexC = $zindexC->getId();
		}
		
		
		/** Position d) **/
		$positionD = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition d) Function "zindex"',
				'formel' => 'zindex[a]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionD = ObjectFactory::insert($positionD);
		if ($positionD->getId())
		{
			Registry::getInstance()->zindexPositionD = $positionD->getId();
		}
		$vars = $positionD->getVariables();
		$vars[0]->setLink($positionA);
		ObjectFactory::update($vars[0]);
		
		/** Position e) **/
		$positionE = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition e) Function "zindex"',
				'formel' => 'zindex[a]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionE = ObjectFactory::insert($positionE);
		if ($positionE->getId())
		{
			Registry::getInstance()->zindexPositionE = $positionE->getId();
		}
		$vars = $positionE->getVariables();
		$vars[0]->setLink($positionB);
		ObjectFactory::update($vars[0]);
		
		/** Position f) **/
		$positionF = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition f) Function "zindex"',
				'formel' => 'zindex[a]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionF = ObjectFactory::insert($positionF);
		if ($positionF->getId())
		{
			Registry::getInstance()->zindexPositionF = $positionF->getId();
		}
		$vars = $positionF->getVariables();
		$vars[0]->setLink($positionC);
		ObjectFactory::update($vars[0]);
		
	}
	
	/**
	 * Erstelle Testdaten für Testing von "average"
	 *
	 * @return void
	 */
	public function testCreateTestDataAverage()
	{
		/** Position a) **/
		$positionA = ObjectFactory::create(
			'position',
			array(
				'name' => 'Testposition a) Function "zindex"',
				'formel' => 'average[1;2;3;4;5;6;7;8;9]',
				'object' => ObjectFactory::getById(2)
			)
		);
		$positionA = ObjectFactory::insert($positionA);
		if ($positionA->getId())
		{
			Registry::getInstance()->averagePositionA = $positionA->getId();
		}
	}
	
}

