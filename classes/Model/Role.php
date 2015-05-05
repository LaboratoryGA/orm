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
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

/**
 * This model represents a role to which a user may be assigned
 *
 * @author Nathan Crause <nathan at crause.name>
 */
class Role extends Model {
	
	public static function loadMetadata(ClassMetadata $metadata) {
		$builder = new ClassMetadataBuilder($metadata);
		
		$builder->setTable('roles');
		$builder->createField('id', 'integer')
				->columnName('roleid')
				->makePrimaryKey()
				->generatedValue()
				->build();
		$builder->createField('name', string)
				->columnName('rolename')
				->length(255)
				->nullable()
				->build();
	}
	
	/**
	 * @var int
	 */
	protected $id;
	
	/**
	 * 
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Textual name of the role
	 */
	protected $name;
	
	/**
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * 
	 * @param string $name
	 * @return \fiveht\models\Role
	 */
	public function setName($name) {
		$this->name = $name;
		
		return $this;
	}
	
	public function __toString() {
		return $this->name;
	}
	
}
