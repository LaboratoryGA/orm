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

use Closure;
use Doctrine\DBAL\Schema\Schema;

/**
 * This class provides a means of creating tables in a more advanced and robust
 * way to Claromentis' "Claromentis\Core\DAL\Schema\SchemaDb" class.
 *
 * @author Nathan Crause <nathan at crause.name>
 */
class Migrator {
	
	/**
	 *
	 * @var \Doctrine\DBAL\Connection 
	 */
	private $connection;
	
	/**
	 *
	 * @var \Doctrine\DBAL\Schema\AbstractSchemaManager
	 */
	private $schemaManager;
	
	public function __construct() {
		$this->connection = EntityManagerFactory::get()->getConnection();
		$this->schemaManager = $this->connection->getSchemaManager();
	}
	
	/**
	 * Invoke this method from within your migration scripts to perform the
	 * actual build instructions.
	 * 
	 * @param Closure $closure function which is invoked to actually
	 * perform the migration up. It will be passed a single parameter, namely
	 * an instance of Doctrine\DBAL\Schema\Schema
	 */
	public function up(Closure $closure) {
		$fromSchema = $this->schemaManager->createSchema();
		$toSchema = clone $fromSchema;
		
		$closure($toSchema);
		
		// apply all the changes
		$this->commitChanges($fromSchema, $toSchema);
	}
	
	private function commitChanges(Schema $fromSchema, Schema $toSchema) {
		$comparator = new \Doctrine\DBAL\Schema\Comparator();
		$schemaDiff = $comparator->compare($fromSchema, $toSchema);
		
		$queries = $schemaDiff->toSql($this->schemaManager->getDatabasePlatform());

		foreach ($queries as $query) {
			$this->connection->exec($query);
		}
	}
	
}
