<?php
namespace Claromentis\Orm\Types;

use DateTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * This mapping type represents a Unix timestamp (seconds since epoch)
 *
 * @author Nathan Crause
 */
class TimestampType extends Type {
    
    public function getName() {
        return 'timestamp';
    }
    
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
        return 'int';
    }
    
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        return date_timestamp_set(date_create(), intval($value));
    }
    
    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        return $value->getTimestamp();
    }
    
}

