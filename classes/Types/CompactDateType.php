<?php
namespace Claromentis\Orm\Types;

use DateTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * This mapping type represents a date (no time) where the elements have been
 * compressed into a single numeric value.
 * <p>
 * For example, the date "2014-06-15" will be compacted into
 * "20140615"
 *
 * @author Nathan Crause
 */
class CompactDateType extends Type {
    
    public function getName() {
        return 'compactdate';
    }
    
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
        return 'decimal(8, 0)';
    }
    
    public function convertToPHPValue($value, AbstractPlatform $platform) {
		$value = floatval($value);	//make sure it's numeric
		
		// break into components
		$value -= ($year =		floor($value / ($divisor = 10000.0))) * $divisor;
		$value -= ($month =		floor($value / ($divisor = 100.0))) * $divisor;
		$day = intval($value);
		
		return date_timestamp_set(date_create(), mktime(0, 0, 0, $month, $day, $year));
    }
    
    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
		/** @var $value DateTime  */
		return $value->format('Ymd');
    }
    
}

