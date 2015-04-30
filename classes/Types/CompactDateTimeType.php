<?php
namespace Claromentis\Orm\Types;

use DateTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * This mapping type represents a date time where the elements have been
 * compressed into a single numeric value.
 * <p>
 * For example, the date time "2014-06-15 12:01:03" will be compacted into
 * "20140615120103"
 *
 * @author Nathan Crause
 */
class CompactDateTimeType extends Type {
    
    public function getName() {
        return 'compactdatetime';
    }
    
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
        return 'decimal(14, 0)';
    }
    
    public function convertToPHPValue($value, AbstractPlatform $platform) {
		$value = floatval($value);	//make sure it's numeric
		
		// break into components
		$value -= ($year =		floor($value / ($divisor = 10000000000.0))) * $divisor;
		$value -= ($month =		floor($value / ($divisor = 100000000.0))) * $divisor;
		$value -= ($day =		floor($value / ($divisor = 1000000.0))) * $divisor;
		$value -= ($hour =		floor($value / ($divisor = 10000.0))) * $divisor;
		$value -= ($minute =	floor($value / ($divisor = 100.0))) * $divisor;
		$second = intval($value);
		
		return date_timestamp_set(date_create(), mktime($hour, $minute, $second, $month, $day, $year));
    }
    
    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
		/* @var $value DateTime  */
		return $value->format('YmdHis');
    }
	
    public function getBindingType() {
        return \PDO::PARAM_INT;
    }
    
}

