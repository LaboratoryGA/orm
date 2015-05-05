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

namespace Claromentis\Orm\Model;

use Claromentis\Orm\Model;
use Claromentis\Orm\Util\String;
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
		$builder->createField('id', 'integer')
				->makePrimaryKey()
				->generatedValue()
				->build();
		$builder->createField('subject', 'string')
				->columnName('username')
				->length(100)
				->nullable()
				->build();
		$builder->createField('credentials', 'string')
				->columnName('password')
				->length(100)
				->nullable()
				->build();
		$builder->createField('familyName', 'string')
				->columnName('surname')
				->length(200)
				->nullable()
				->build();
		$builder->createField('givenName', 'string')
				->columnName('firstname')
				->length(200)
				->nullable()
				->build();
		
		$builder->createManyToMany('groups', 'Claromentis\Orm\Model\Group')
				->setJoinTable('user_groups')
				->addInverseJoinColumn('groupid', 'groupid')
				->addJoinColumn('userid', 'id')
				->fetchExtraLazy()
				->build();
		
		$builder->createManyToMany('roles', 'Claromentis\Orm\Model\Role')
				->setJoinTable('user_roles')
				->addInverseJoinColumn('roleid', 'roleid')
				->addJoinColumn('userid', 'id')
				->fetchExtraLazy()
				->build();
		
//		echo '<pre>' . print_r($metadata, true) . '</pre>';
	}
	
	public function __construct() {
		$this->groups = new ArrayCollection();
		$this->roles = new ArrayCollection();
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
	
	/**
	 *
	 * @var ArrayCollection
	 */
	protected $groups;
	
	/**
	 * 
	 * @return ArrayCollection
	 */
	public function getGroups() {
		return $this->groups;
	}
	
	/**
	 *
	 * @var ArrayCollection
	 */
	protected $roles;
	
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
	
	/**
	 * Checks the user to see if they belong to the group (or groups).
	 * <p>
	 * If a string is passed, then the group is located using the group's
	 * name.
	 * <p>
	 * If an number is passed, then the group is located by ID.
	 * <p>
	 * If an array is passed, each individual element is passed back to the
	 * function (via recursion), and the previous 3 tests apply.
	 * 
	 * @param Group|string|number|array $group either a group object to compare
	 * against, or the string name of the group, or an integer of the group's ID
	 * or an array containing either a list of group objects, or strings 
	 * containing the group names, or numbers containing the IDs
	 * @return boolean <code>true</code> if this user belongs to one or more
	 * of the supplied groups
	 */
	public function belongsTo($group) {
		if ($group instanceof Group) {
//			die("HALT!". print_r($this->groups, true));
//			return $this->groups->exists($group);
			foreach ($this->groups as $g) {
				if ($g->getId() == $group->getId()) {
					return true;
				}
			}
			
			return false;
		}
		elseif (is_numeric($group)) {
			if (!($found = Group::find($group))) {
				throw new Exception("Unable to locate group using ID '$group'");
			}
			
			return $this->belongsTo($found);
		}
		elseif (is_string($group)) {
			if (!($found = Group::findOneBy(array('name' => $group)))) {
				throw new Exception("Unable to locate group using name '$group'");
			}
			
			return $this->belongsTo($found);
		}
		elseif (is_array($group)) {
			foreach ($group as $g) {
				if ($this->belongsTo($g)) {
					return true;
				}
			}
			
			return false;
		}
		die("Received a ... " . gettype($group));
		
		
		throw new Exception('Unexpected group test type ' . gettype($group));
	}
	
	public function __toString() {
		return $this->getFullName() 
				. ' - Groups: (' . String::valueOf($this->groups) . ')'
				. ' - Roles: (' . String::valueOf($this->roles) . ')';
	}
	
}
