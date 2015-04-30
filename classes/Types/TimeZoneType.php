<?php
namespace Claromentis\Orm\Types;

use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Custom doctrine type for retrieving and storing time-zone references
 *
 * @author Nathan Crause
 * @version 1.0
 */
class TimeZoneType extends Type {
    
    public function getName() {
        return 'timezone';
    }
    
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
        return 'varchar(255)';
    }
    
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        return new DateTimeZone($value);
    }
    
    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        return $value->getName();
    }
	
}
