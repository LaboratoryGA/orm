<?php
namespace Claromentis\Orm;

use PDO;

/**
 * This class holds the information for a data provider
 */
class DataSource {
	
	public static $DRIVER_MAPPING = array(
		'mssql' => 'sqlsrv',
		'mssql_nc' => 'sqlsrv'
	);
	
	public static $DSN_FORMAT = array(
		'mysql' => '%1$s:host=%2$s;dbname=%3$s',
		'sqlsrv' => '%1$s:Server=%2$s;Database=%3$s'
	);
	
	public static $DRIVER_OPTIONS = array(
		'mysql' => array(1002 => "SET NAMES utf8"), //PDO::MYSQL_ATTR_INIT_COMMAND = 1002 - we use the actual value here in case MySQL PDO hasn't been loaded
		'sqlsrv' => array()
	);
	
	private $driver;
	
	public function getDriver() {
		return $this->driver;
		
	}
	
	public function getRealDriver() {
		if (key_exists($this->driver, static::$DRIVER_MAPPING)) {
			return static::$DRIVER_MAPPING[$this->driver];
		}
		
		return $this->driver;
	}
	
	private $hostname;
	
	public function getHostname() {
		return $this->hostname;
	}
	
	private $schema;
	
	public function getSchema() {
		return $this->schema;
	}
	
	private $authentication;
	
	/**
	 * 
	 * @return Authentication
	 */
	public function getAuthentication() {
		return $this->authentication;
	}
	
	public function __construct($driver, $hostname, $schema, Authentication $authentication) {
		$this->driver = $driver;
		$this->hostname = $hostname;
		$this->schema= $schema;
		$this->authentication = $authentication;
	}
	
	public function __toString() {
		$driver = $this->driver;
		
		if (key_exists($driver, static::$DRIVER_MAPPING)) {
			$driver = static::$DRIVER_MAPPING[$driver];
		}
		
		return "{$this->authentication}@{$driver}://{$this->hostname}/{$this->schema}";
	}
	
	/**
	 * Utility method which opens a PDO connection to this data source
	 * 
	 * @return PDO
	 */
	public function openConnection() {
		$driver = $this->getRealDriver();
		$pdo = new PDO(sprintf(static::$DSN_FORMAT[$driver], $driver, $this->hostname, $this->schema),
				$this->getAuthentication()->getSubject(),
				$this->getAuthentication()->getCredentials(),
				static::$DRIVER_OPTIONS[$driver]);
		
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		return $pdo;
	}
	
	/**
	 * Creates a Doctrine2 DBAL connection to the database
	 */
	public function openDBAL() {
		return \Doctrine\DBAL\DriverManager::getConnection($this->toConnectionParams(), $config);
	}
	
	/**
	 * Converts the values within this data source instance to an associative
	 * array for use in DBAL and ORM connections
	 */
	public function toConnectionParams() {
		$params = [
			'dbname' => $this->schema,
			'user' => $this->getAuthentication()->getSubject(),
			'password' => $this->getAuthentication()->getCredentials(),
			'host' => $this->hostname,
			'driver' => 'pdo_' . ($driver = $this->getRealDriver())
		];
		
		if ($driver == 'mysql') {
			$params['charset'] = 'utf8';
		}
		
		return $params;
	}
	
}
