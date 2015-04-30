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

namespace Claromentis\Orm\Models;

use Claromentis\Orm\Model;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

/**
 * This model represents a single user
 *
 * @author Nathan Crause <nathan at crause.name>
 * @method User find(mixed $$id, int $lockMode = \Doctrine\DBAL\LockMode::NONE) Find a single model by it's unique ID
 */
class User extends Model {
	
	public static function loadMetadata(ClassMetadata $metadata) {
		$builder = new ClassMetadataBuilder($metadata);
		
		$builder->setTable('users');
		$builder->createField('id', 'integer')->isPrimaryKey()->generatedValue()->build();
		$builder->addField('subject', 'string', [
			'columnName'	=> 'username',
			'length'		=> 100,
			'nullable'		=> true
		]);
		$builder->addField('credentials', 'string', [
			'columnName'	=> 'password',
			'length'		=> 50,
			'nullable'		=> true
		]);
		$builder->addField('familyName', 'string', [
			'columnName'	=> 'surname',
			'length'		=> 200,
			'nullable'		=> true
		]);
		$builder->addField('givenName', 'string', [
			'columnName'	=> 'firstname',
			'length'		=> 200,
			'nullable'		=> true
		]);
		
//		echo '<pre>' . print_r($metadata, true) . '</pre>';
	}
	
	public function __construct() {
//		$this->roles = new ArrayCollection();
//		$this->groups = new ArrayCollection();
//		$this->bosses = new ArrayCollection();
	}
	
	/**
	 * @var int
	 */
	protected $id;
	
	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @var string
	 */
	protected $subject;
	
	/**
	 * @return string
	 */
	public function getSubject() {
		return $this->subject;
	}
	
	/**
	 * @param string $subject
	 * @return User
	 */
	public function setSubject($subject) {
		$this->subject = $subject;
		
		return $this;
	}
	
	/**
	 * @var string
	 */
	protected $credentials;
	
	/**
	 * @return string
	 */
	public function getCredentials() {
		return $this->credentials;
	}
	
	/**
	 * @param string $credentials
	 * @return User
	 */
	public function setCredentials($credentials) {
		$this->credentials = $credentials;
		
		return $this;
	}
	
	/**
	 * @var string
	 * @Column(name="surname", type="string", length=200, nullable=true)
	 */
	protected $familyName;
	
	/**
	 * @return string
	 */
	public function getFamilyName() {
		return $this->familyName;
	}
	
	/**
	 * @param string $familyName
	 * @return User
	 */
	public function setFamilyName($familyName) {
		$this->familyName = $familyName;
		
		return $this;
	}
	
	/**
	 * @var string
	 * @Column(name="firstname", type="string", length=200, nullable=true)
	 */
	protected $givenName;
	
	/**
	 * @return string
	 */
	public function getGivenName() {
		return $this->givenName;
	}
	
	/**
	 * @param string $givenName
	 * @return User
	 */
	public function setGivenName($givenName) {
		$this->givenName = $givenName;
		
		return $this;
	}
	
	public function getFullName() {
		$parts = [];
		
		if ($this->givenName) $parts[] = $this->givenName;
		if ($this->familyName) $parts[] = $this->familyName;
		
		return implode(' ', $parts);
	}
	
	/**
	 * Generates a URL pointing to the photo/image associated with this user.
	 * 
	 * @return string absolute URL path
	 */
	public function getProfileImage() {
		$cdn = \Claromentis\Core\Services::I()->GetCDN();
		
		return $cdn->GetURL("people/{$this->getId()}.jpg")
				?: '/appdata/people/no_photo.jpg';
	}
	
	/**
	 * Generates a URL pointing to the profile page of this user.
	 * 
	 * @return string the full URL with sheme, domain, path
	 */
	public function getProfileURL() {
		return '/intranet/people/viewprofile.php?' 
				. http_build_query(['id' => $this->getId()]);
	}
	
}
