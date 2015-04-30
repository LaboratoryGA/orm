<?php
namespace Claromentis\Orm\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * This is a special custom post type which attempts to offer "enum" support
 * for column types.
 * <p>
 * This type will require database-engine specific mechanisms in place, since
 * (for example), MySQL and PostgreSQL natively supports enum types, but
 * MSSQL, does not.
 * <p>
 * Thanks to http://stackoverflow.com/questions/1434298/sql-server-equivalent-to-mysql-enum-data-type
 * we can use a "check" to simulate this on MSSQL, using a simple varchar column:
 * <pre>
 * mycol VARCHAR(10) NOT NULL CHECK (mycol IN('Useful', 'Useless', 'Unknown'))
 * </pre>
 *
 * @author nathan
 */
class EnumType extends Type {
	
    public function getName() {
        return 'enum';
    }
    
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
		if (is_string(@$fieldDeclaration['comment'])) {
			$fieldDeclaration['comment'] = preg_split('/\s*,\s*', $fieldDeclaration['comment']);
		}
		if (!is_array(@$fieldDeclaration['comment'])) {
			throw new \Exception('Must supply an array of "comment" values, which are the valid values of the enum');
		}
		
		$em = \fiveht\EntityManagerFactory::get();
		$conn = $em->getConnection();
		
		if ($platform instanceof \Doctrine\DBAL\Platforms\MySqlPlatform) {
			return 'ENUM(' . implode(', ', array_map(function($value) use ($conn) {
					return $conn->quote($value);
				}, $fieldDeclaration['comment']))
			. ')';
		}
		elseif ($platform instanceof \Doctrine\DBAL\Platforms\SQLServerPlatform) {
//			return $platform->getVarcharTypeDeclarationSQL(array('length' => 255));
			return 'VARCHAR(255) ' 
					. (!@$fieldDeclaration['notnull'] ? 'NOT NULL ' : '')
					. "CHECK ({$platform->quoteIdentifier($fieldDeclaration['name'])} IN (" 
					. implode(', ', array_map(function($value) use ($conn) {
							return $conn->quote($value);
						}, $fieldDeclaration['comment'])) 
					. '))';
		}
		else {
			throw new \Exception('Unsupported platform: ' . get_class($platform));
		}
    }
    
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        return $value;
    }
    
    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        return $value;
    }
	
}
