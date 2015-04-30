<?php
namespace Claromentis\Orm;

use Exception;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\Driver\StaticPHPDriver;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

/**
 * This class should be used to get an instance of a Doctrine
 * entity manager.
 */
final class EntityManagerFactory {
	
	/**
	 * The last EntityManager instantiated using this class
	 * 
	 * @var EntityManager
	 */
	private static $last;
	
	/**
	 * Retrieves/creates an EntityManager. If no previous EntityManager
	 * was instantiated, a new one is always created. If <code>$new</code>
	 * is <code>true</code>, then creation of a new EntityManager is
	 * forced. Otherwise, this method will simply return the last EntityManager
	 * created.
	 * 
	 * @param boolean $new flag if instantiation of a new EntityManager object
	 * is forced
	 * @return EntityManager
	 */
	public static function get($new = false) {
		// make sure the ORM has been initialized
		if (!defined('ORM_INITIALIZED')) {
			\ClaApplication::Enter('orm');
		}
		
		// if we've never been invoked before, load up all the custom data types
		if (!self::$last) {
			self::registerTypes();
		}
		
		if (self::$last && !$new) {
			return self::$last;
		}
		
//		$driver = Configuration::get()->getDataSource()->getRealDriver();
//		
//		self::$last = EntityManager::create([
//            'driver' => 'pdo_' . $driver,
//            'host' => Configuration::get()->getDataSource()->getHostname(),
//            'user' => Configuration::get()->getDataSource()->getAuthentication()->getSubject(),
//            'password' => Configuration::get()->getDataSource()->getAuthentication()->getCredentials(),
//            'dbname' => Configuration::get()->getDataSource()->getSchema()
//        ], $config = self::initConfig());
		self::$last = EntityManager::create(
				Configuration::get()->getDataSource()->toConnectionParams(), 
				$config = self::initConfig());
		
		self::registerDoctrineTypeMappings(self::$last->getConnection());
		self::registerNumericFunctions($config);
		
		return self::$last;
	}
	
	public static function __getLast() {
		return self::$last;
	}
    
    /**
     * Creates a Doctrine configuration object
	 * 
     * @return \Doctrine\ORM\Configuration 
     */
    private static function initConfig() {
		global $cfg_model_dirs;
    	// if we're running from the command-line (as would be the case in unit testing),
    	// just use an in-memory array - otherwise store the cache files in the OS's
    	// temp directory
        $cache = self::getCache();
        
        // If we've received a "PURGE" request
        if (key_exists('FLUSH', $_GET)) {
            $cache->flushAll();
        }
		
		$config = Setup::createConfiguration(false, self::getTmpDir(), $cache);
        
		// we don't actually need to set directories - Claromentis' classloader
		// will load the necessary files
//		$config->setMetadataDriverImpl(new StaticPHPDriver($cfg_model_dirs));
		$config->setMetadataDriverImpl(new StaticPHPDriver([]));
        $config->setAutoGenerateProxyClasses(true);
        
        return $config;
    }
	
	private static function getTmpDir() {
		global $APPDATA;
		
		return $APPDATA . '/orm';
	}

	/**
	 * 
	 * @return \Doctrine\Common\Cache\Cache
	 */
	private static function getCache() {
		return (php_sapi_name() == 'cli')
				? new ArrayCache()
				: new Cache();
//		return new ArrayCache();
	}
	
	private static function registerTypes() {
		Type::addType('compactdate', 'Claromentis\Orm\Types\CompactDateType');
		Type::addType('compactdatetime', 'Claromentis\Orm\Types\CompactDateTimeType');
		Type::addType('hexcolor', 'Claromentis\Orm\Types\HexColorType');
		Type::addType('timestamp', 'Claromentis\Orm\Types\TimestampType');
		Type::addType('timezone', 'Claromentis\Orm\Types\TimeZoneType');
		Type::addType('enum', 'Claromentis\Orm\Types\EnumType');
	}
	
	private static function registerDoctrineTypeMappings($connection) {
		$connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'enum');
	}
	
	private static function registerNumericFunctions($config) {
		$config->addCustomNumericFunction('FLOOR', 'Claromentis\Orm\Functions\Floor');
	}
	
}
