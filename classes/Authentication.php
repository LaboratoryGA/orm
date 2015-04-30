<?php
namespace Claromentis\Orm;

/**
 * Resource access credentials
 */
class Authentication {
	
	private $subject;
	
	public function getSubject() {
		return $this->subject;
	}
	
	private $credentials;
	
	public function getCredentials() {
		return $this->credentials;
	}
	
	public function __construct($subject, $credentials) {
		$this->subject = $subject;
		$this->credentials = $credentials;
	}
	
	public function __toString() {
		return "{$this->subject}:{$this->credentials}";
	}
	
}
