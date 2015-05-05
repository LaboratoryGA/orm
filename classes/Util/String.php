<?php

/*
 * Copyright (C) 2015 Nathan Crause <nathan at crause.name>
 *
 * This file is part of ORM
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Claromentis\Orm\Util;

/**
 * This class provides methods for converting various types of information
 * to a string.
 *
 * @author Nathan Crause <nathan at crause.name>
 */
class String {
	
	public static function valueOf($val) {
		if (is_null($val)) {
			return 'NULL';
		}
		// leave strings unchanges
		if (is_string($val)) {
			return $val;
		}
		// convert booleans to "true"/"false"
		if (is_bool($val)) {
			return $val ? 'true' : 'false';
		}
		// if it's an array, pass each element (and it's key) to "toString"
		// and implode it
		if (is_array($val)) {
			return self::valueOfArray($val);
		}
		// all other scalar values, simply cast
		if (is_scalar($val)) {
			return (string) $val;
		}
		// if it's an "resource", return it's resource type
		if (is_resource($var)) {
			return 'resource(' . get_resource_type($var) . ')';
		}
		// if it's an object, check if there is a "__toString()" method
		if (is_object($val)) {
			return self::valueOfObject($val);
		}
		
		throw new \Exception('Unstringable value');;
	}
	
	public static function valueOfArray(array $val) {
		$return = [];

		foreach ($val as $k => $v) {
			$return[] = self::valueOf($k) . ': ' . self::valueOf($v);
		}

		return implode(', ', $return);
	}
	
	public static function valueOfObject($val) {
		assert(is_object($val));
		
		// if "__toString" exists, we can simply cast the object
		if (method_exists($val, '__toString')) {
			return (string) $val;
		}
		// ** From this point, we check for specific classes to handle
		if ($val instanceof \Doctrine\Common\Collections\Collection) {
			return self::valueOf($val->toArray());
		}
		
		// all else failed, so imply return some generic result
		return 'object(' . get_class($val) . ')';
	}
	
}
