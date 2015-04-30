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

namespace Claromentis\Orm;

/**
 * This is a superclass from which all model implementations can implement in
 * order to simplify a lot of querying and interaction.
 *
 * @author Nathan Crause <nathan at crause.name>
 */
class Model {
	
	/**
	 * This convenience method instantiates the model (using late static
	 * binding to determine the actual class) and sets all the columns
	 * supplied in the associative array.
	 * <p>
	 * Note that this method presumes the model as a no-argument constructor
	 * 
	 * @param array $columns associative array of column name + value
	 * @return BaseModel
	 */
	public static function create(array $columns) {
		$class = get_called_class();
		$instance = new $class();
		
		$instance->setColumns($columns);
		
		return $instance;
	}
	
	public function setColumns(array $columns) {
		foreach ($columns as $name => $value) {
			// look for a "setXXX" method, and if it exists, invoke it
			if (method_exists($this, $method = 'set' . ucfirst($name))) {
				call_user_func(array($this, $method), $value);
			}
		}
		
		return $this;
	}
	
	/**
	 * Retrieves this model's repository.
	 * <p>
	 * This method uses late static binding to support subclasses (using
	 * <code>get_called_class()</code>) such that any subclass of this
	 * class will have their particular repository returned, instead of
	 * the superclass's.
	 * 
	 * @global \mvc\models\Doctrine\ORM\EntityManager $entities
	 * @return \Doctrine\ORM\EntityRepository
	 */
	public static function getRepository() {
		/* @var $entities Doctrine\ORM\EntityManager */
		global $entities;
		
		$entities = $entities ?: EntityManagerFactory::get();
		
		return $entities->getRepository(get_called_class());
	}
	
	/**
	 * 
	 * @param mixed $id identity of the model to search for
	 * @param int $lockMode one of \Doctrine\DBAL\LockMode::???
	 * @return BaseModel
	 */
	public static function find($id, $lockMode = \Doctrine\DBAL\LockMode::NONE) {
		return static::getRepository()->find($id, $lockMode);
	}
	
	/**
	 * 
	 * @return array(BaseModel)
	 */
	public static function findAll() {
		return static::getRepository()->findAll();
	}
	
	/**
	 * 
	 * @param array $criteria
	 * @param array $orderBy
	 * @param int $limit
	 * @param int $offset
	 * @return array(BaseModel)
	 */
	public static function findBy(array $criteria, array $orderBy = null, 
			$limit = null, $offset = null) {
		return static::getRepository()
				->findBy($criteria, $orderBy, $limit, $offset);
	}
	
	/**
	 * 
	 * @param array $criteria
	 * @param array $orderBy
	 * @return BaseModel
	 */
	public static function findOneBy(array $criteria, array $orderBy = null) {
		return static::getRepository()->findOneBy($criteria, $orderBy);
	}
	
	/**
	 * 
	 * @param string $alias DQL alias for this entity
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public static function buildQuery($alias) {
		return static::getRepository()->createQueryBuilder($alias);
	}
	
	public static function matching(\Doctrine\Common\Collections\Criteria $criteria) {
		return static::getRepository()->matching($criteria);
	}
	
	/**
	 * 
	 * @global \mvc\models\Doctrine\ORM\EntityManager $entities
	 * @return \Doctrine\ORM\Mapping\ClassMetadata
	 */
	public static function getMetadata() {
		/* @var $entities Doctrine\ORM\EntityManager */
		global $entities;
		
		$entities = $entities ?: EntityManagerFactory::get();
		
		return $entities->getClassMetadata(get_called_class());
	}
	
	public static function __callStatic($name, $arguments) {
		return call_user_func_array(array(static::getRepository(), $name), $arguments);
	}
	
	/**
	 * 
	 * @global \mvc\models\Doctrine\ORM\EntityManager $entities
	 * @param boolean $flush set to <code>true</code> to flush the save
	 * immediately
	 * @return BaseModel
	 */
	public function save($flush = true) {
		/* @var $entities Doctrine\ORM\EntityManager */
		global $entities;
		
		$entities = $entities ?: EntityManagerFactory::get();
		
		$entities->persist($this);
		
		if ($flush) {
			$entities->flush();
		}
		
		return $this;
	}
	
	public function detach() {
		EntityManagerFactory::get()->detach($this);
		
		return $this;
	}
	
	public function refresh() {
		EntityManagerFactory::get()->refresh($this);
	}
	
	public function toArray($deep = true) {
		$meta = static::getMetadata();
		$return = array();
		
		foreach ($meta->getFieldNames() as $field) {
			$return[$field] = $meta->getFieldValue($this, $field);
		}
		
		foreach ($meta->getAssociationMappings() as $assoc) {
//			print_r($assoc);
			
			$field = $assoc['fieldName'];
			$value = $meta->getFieldValue($this, $assoc['fieldName']);
			
			if ($value instanceof \Doctrine\Common\Collections\Collection) {
				if ($deep) {
					$values = array();

					foreach ($value->toArray() as $joined) {
							$values[] = $joined->toArray(false);
					}

					$return[$field] = $values;
				}
				else {
					$return[$field] = '*TOO DEEP*';
				}
			}
			else {
				$return[$field] = $value;
			}
		}
		
		return $return;
	}
}
