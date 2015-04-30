<?php

/*
 * Copyright (C) 2014 FiveHT Media Ltd. - All right reserved.
 * 
 * The file RGBColor.php is part of 5ht_csl
 * 
 * Copying, modification, duplication in whole or in part without
 * the express written consent of FiveHT Media Ltd. is prohibited.
 */

namespace Claromentis\Orm\Types;

use RuntimeException;

/**
 * Utility class for holding RGB colour references, with various import/export
 * options.
 *
 * @author Nathan Crause
 * @version 1.0
 */
class RGBColor {
	
	/**
	 * The 0-255 red component
	 *
	 * @var integer
	 */
	protected $red;
	
	/**
	 * Retrieve the red component
	 * 
	 * @return integer
	 */
	public function getRed() {
		return $this->red;
	}

	/**
	 * Sets the red color component.
	 * 
	 * @param type $red
	 * @return \fiveht\ui\RGBColor
	 * @throws RuntimeException if the value supplied is invalid
	 */
	public function setRed($red) {
		if (!is_numeric($red) || ($red < 0 || $red > 255)) {
			throw new RunException("Incorrect red element specified: must be a number between 0-255, '$red' supplied");
		}
		
		$this->red = $red;
		return $this;
	}

	/**
	 * The 0-255 green component
	 *
	 * @var integer
	 */
	protected $green;
	
	/**
	 * Retrieve the green component
	 * 
	 * @return integer
	 */
	public function getGreen() {
		return $this->green;
	}

	/**
	 * Sets the green color component.
	 * 
	 * @param type $green
	 * @return \fiveht\ui\RGBColor
	 * @throws RuntimeException if the value supplied is invalid
	 */
	public function setGreen($green) {
		if (!is_numeric($green) || ($green < 0 || $green > 255)) {
			throw new RunException("Incorrect green element specified: must be a number between 0-255, '$green' supplied");
		}
		
		$this->green = $green;
		return $this;
	}

	/**
	 * The 0-255 blue component
	 *
	 * @var integer
	 */
	protected $blue;
	
	/**
	 * Retrieve the blue component
	 * 
	 * @return integer
	 */
	public function getBlue() {
		return $this->blue;
	}

	/**
	 * Sets the blue color component.
	 * 
	 * @param type $blue
	 * @return \fiveht\ui\RGBColor
	 * @throws RuntimeException if the value supplied is invalid
	 */
	public function setBlue($blue) {
		if (!is_numeric($blue) || ($blue < 0 || $blue > 255)) {
			throw new RunException("Incorrect blue element specified: must be a number between 0-255, '$blue' supplied");
		}
		
		$this->blue = $blue;
		return $this;
	}
	
	/**
	 * Converts the RGB colors of this instance into a hex-encoded string.
	 * 
	 * @param boolean $htmlAnnotation if <code>true</code>, then the character
	 * "#" is prefixed to the result (suitable for CSS/HTML)
	 */
	public function toHex($htmlAnnotation = false) {
		$hex = self::fixedLengthHex($this->getRed())
				. self::fixedLengthHex($this->getGreen())
				. self::fixedLengthHex($this->getBlue());
		
		if ($htmlAnnotation) {
			$hex = '#' . $hex;
		}
		
		return $hex;
	}
	
	private static function fixedLengthHex($number, $targetLength = 2) {
		$hex = dechex($number);
		
		while (strlen($hex) < $targetLength) {
			$hex = '0' . $hex;
		}
		
		return $hex;
	}
	
	function __construct($red, $green, $blue) {
		$this->red = $red;
		$this->green = $green;
		$this->blue = $blue;
	}

	/**
	 * Parses a hex-encoded color into an RGB color.
	 * <p>
	 * If the value starts with a "#" (per HTML notation), it is automatically 
	 * stripped out.
	 * 
	 * @param string $value
	 */
	public static function parseHex($value) {
		if (!$value) return null;
		
		// remove optional leading "#"
		$value = strtoupper(ltrim($value, '#'));
		
		if (($len = strlen($value)) != 6) {
			// if we have NO characters, just return a NULL object
			if (!$len) return null;
			
			throw new RuntimeException("Malformed hexidecimal color - must 6 characters long, $len supplied ($value)");
		}
		
		if (!preg_match('/[0-9A-F]{6}/', $value)) {
			throw new RuntimeException("Malformed hexidecimal color - must only contain the characters 0-9 and A-F ($value)");
		}
		
		// split into 3 equal parts
		$red = hexdec(substr($value, 0, 2));
		$green = hexdec(substr($value, 2, 2));
		$blue = hexdec(substr($value, 4, 2));
		
		return new self($red, $green, $blue);
	}
	
	public function __toString() {
		return $this->toHex();
	}
	
}
