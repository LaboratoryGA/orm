<?php
namespace Claromentis\Orm\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Custom Doctrine type for a field which contains a hex-encoded colour.
 * <p>
 * The database value itself also contains the leading "#", per HTML notation.
 *
 * @author Nathan Crause
 * @version 1.0
 */
class HexColorType extends Type {
    
    public function getName() {
        return 'hexcolor';
    }
    
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
        return 'varchar(7)';
    }
    
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        return RGBColor::parseHex($value);
    }
    
    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        return $value ? $value->toHex(true) : null;
    }
	
}
