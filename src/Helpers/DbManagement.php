<?php

namespace Core\Helpers;

/**
 * Class DbManagement
 *
 * @category Core
 * @package  Core\Helpers
 * @author   Ionel-Alex Caizer <ionel@dreiwerken.de>
 */
class DbManagement
{

	/**
	 * Beinhaltet die Informationen zum Pfad
	 *
	 * @var string
	 */
	private $path;

	/**
	 * SQL-Statements aus dem DIFF heraus.
	 *
	 * @var array
	 */
	private $response = array();

	/**
	 * CREATE-Statements
	 *
	 * @var array
	 */
	private $creates = array();

	/**
	 * ALTER-Statements
	 *
	 * @var array
	 */
	private $alters = array();

	/**
	 * DROP-Statements
	 *
	 * @var array
	 */
	private $drops = array();

	/**
	 * Speichert die Tabellennamen die durch die Änderungen betroffen wären
	 *
	 * @var array
	 */
	private $effectedTables = array();

	/**
	 * Schema-Tool Instanz
	 *
	 * @var \Doctrine\ORM\Tools\SchemaTool
	 */
	private $schemaTool = null;

	/**
	 * Entity-Manager Instanz
	 *
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $entityManager = null;

	/**
	 * Flag zum Speichern, ob Aktionen erfolgreich waren oder nicht
	 *
	 * @var boolean
	 */
	private $success = true;

	/**
	 * Konstruktor
 	 */
	public function __construct()
	{
		// Doctrine in den ClassLoader holen
		$loader = new \Core\Loader('Doctrine', FRAMEWORK_PATH);
		$loader->register();

		// ORM Configuration
		$config = new \Doctrine\ORM\Configuration();
		$config->setProxyDir(ROOT_PATH . '/tmp/proxies');
		$config->setProxyNamespace('Proxy');
		$driverImpl = $config->newDefaultAnnotationDriver(array(APPLICATION_PATH . '/Models'));
		$config->setMetadataDriverImpl($driverImpl);

		// Connection Options definieren
		$connectionOptions = array(
			'driver' => 'pdo_mysql',
			'dbname' => DB_DATABASE,
			'user' => DB_USER,
			'password' => DB_PASSWORD,
			'host' => DB_SERVER
		);

		$event = new \Doctrine\Common\EventManager();
		$event->addEventSubscriber(new \Doctrine\DBAL\Event\Listeners\MysqlSessionInit('utf8', 'utf8_unicode_ci'));

		// EntityManager
		$em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config, $event);
		$this->entityManager = $em;
		$metadatas = $em->getMetadataFactory()->getAllMetadata();

		// SchemaTool initialisieren und UpdateStatements abholen
		$this->schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
		$this->response = $this->schemaTool->getUpdateSchemaSql($metadatas, false);

		// Statements gruppieren
		$this->group();

	}

	/**
	 * Liefert alle Statements als Array;
	 * Inklusive DROP-Statements
	 *
	 * @return array
	 */
	public function getStatements()
	{
		return $this->response;
	}

	/**
	 * Liefert alle Statements als String
	 * Inklusive DROP-Statements
	 *
	 * @return string
	 */
	public function getStatementsAsString()
	{
		$string = implode(";\n", $this->response);
		$string .= ';';

		return $string;
	}

	/**
	 * Liefert die gruppierten CREATE-Statements
	 *
	 * array[TABELLENNAME][STATEMENTS 1...n]
	 *
	 * @return array
	 */
	public function getCreateStatements()
	{
		return $this->creates;
	}

	/**
	 * Liefert die gruppierten ALTER-Statements
	 *
	 * array[TABELLENNAME][STATEMENTS 1...n]
	 *
	 * @return array:
	 */
	public function getAlterStatements()
	{
		return $this->alters;
	}

	/**
	 * Liefert die gruppierten DROP-Statements
	 *
	 * array[TABELLENNAME][STATEMENTS 1...n]
	 *
	 * @return array:
	 */
	public function getDropStatements()
	{
		return $this->drops;
	}

	/**
	 * Führt die CREATE-STATEMENTS durch.
	 *
	 * Zunächst wird geprüft ob alle CREATE-Statements ausgeführt werde könnten.
	 * (Prüfung ob DROP-Statement für die Tabelle vorhanden ist). Wenn die Überprüfung
	 * fehl schlägt wird eine System-Error-Message erstellt.
	 *
	 * @param boolean $force SQL-Statement auch auf die DB durchführen
	 *
	 * @return boolean
	 */
	public function executeCreates($force = true)
	{
		$check = true;
		foreach ($this->creates as $cStmt)
		{
			foreach ($this->drops as $st)
			{
				if ($st[1] == $cStmt[1])
				{
					\Core\SystemMessages::addError("Erst DROP ausführen.");
					\Core\SystemMessages::addNotice($st[0] . ";");
					$check = false;
				}
			}
		}

		$this->success = $check;

		if (!$force)
		{
			return true;
		}

		if ($check)
		{
			foreach ($this->creates as $index => $cStmt)
			{
				$this->execute($cStmt);
				$this->creates[$index][2] = true;
			}

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Führt die ALTER-STATEMENTS durch.
	 *
	 * Zuerst wird geprüft ob eine vorangehende Aktion erfolgreich war. Wenn nicht wird die Funktion
	 * abgebrochen.
	 *
	 * Zunächst wird geprüft ob alle CREATE-Statements ausgeführt werde könnten.
	 * (Prüfung ob DROP-Statement für die Tabelle vorhanden ist). Wenn die Überprüfung
	 * fehl schlägt wird eine System-Error-Message erstellt.
	 *
	 * @param boolean $force SQL-Statement auch auf die DB durchführen
	 *
	 * @return boolean
	 */
	public function executeAlters($force = true)
	{
		if (!$this->success)
		{
			\Core\SystemMessages::addError("Kann die ALTER-Statements nicht ausführen da eine vorherige Aktion nicht erfolgreich war.");

			return false;
		}

		$check = true;
		foreach ($this->alters as $aStmt)
		{
			foreach ($this->creates as $st)
			{
				if ($st[1] == $aStmt[1] && $st[2] == false)
				{
					\Core\SystemMessages::addError("Erst CREATE ausführen.");
					\Core\SystemMessages::addNotice($st[0] . ";");
					$check = false;
				}
			}
		}

		$this->success = $check;

		if (!$force)
		{
			return true;
		}

		if ($check)
		{
			foreach ($this->alters as $aStmt)
			{
				$this->execute($aStmt);
			}

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Gruppiert die Statements anhand des Statement-Anfangs
	 *
	 * @return void
	 */
	private function group()
	{
		foreach ($this->response as $index => $statement)
		{
			if (\Core\String::startsWith($statement, "ALTER TABLE"))
			{
				//Beginne NACH dem "ALTER TABLE"
				$sub = substr($statement, 12);
				$table = substr($sub, 0, strpos($sub, " "));

				$this->alters[] = array($statement, $table, false);

				if (!array_key_exists($table, $this->effectedTables))
				{
					$this->effectedTables[$table] = 0;
				}

				$this->effectedTables[$table] = $this->effectedTables[$table] + 1;
			}
			else
			{
				if (\Core\String::startsWith($statement, "CREATE TABLE"))
				{
					//Beginne NACH dem "CREATE TABLE"
					$sub = substr($statement, 13);
					$table = substr($sub, 0, strpos($sub, " "));

					$this->creates[] = array($statement, $table, false);

					if (!array_key_exists($table, $this->effectedTables))
					{
						$this->effectedTables[$table] = 0;
					}

					$this->effectedTables[$table] = $this->effectedTables[$table] + 1;
				}
				else
				{
					if (\Core\String::startsWith($statement, "DROP TABLE"))
					{
						//Beginne NACH dem "DROP TABLE"
						$table = substr($statement, 11);

						$this->drops[] = array($statement, $table, false);

						if (!array_key_exists($table, $this->effectedTables))
						{
							$this->effectedTables[$table] = 0;
						}

						$this->effectedTables[$table] = $this->effectedTables[$table] + 1;
					}
				}
			}
		}
	}

	/**
	 * Führt ein SQL-Statement aus, wenn alle vorangehende Aktionen erfolgreich waren.
	 *
	 * @param string $sql
	 *
	 * @throws \ErrorException Wenn das SQL-Statement nicht ausgeführt werden konnte
	 *
	 * @return boolean
	 */
	private function execute($sql)
	{
		if (!$this->success)
		{
			\Core\SystemMessages::addError("Kann das Statement nicht ausführen da eine vorherige Aktion nicht erfolgreich war.");
			\Core\SystemMessages::addNotice($sql);

			return false;
		}

		$conn = $this->entityManager->getConnection();
		try
		{
			$conn->executeQuery($sql);
		} catch (\Exception $e)
		{
			$this->success = false;
			throw new \ErrorException("Schema-Tool failed with Error '" . $e->getMessage() . "' while executing DDL: " . $sql, "0", $e);
		}

		return true;
	}
}
