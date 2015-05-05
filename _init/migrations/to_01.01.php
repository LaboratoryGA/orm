<?php
$_db_migration_to = '01.01';
if (!isset($migrations) || !is_object($migrations))
	die("This file cannot be executed directly");
$migrations->CheckValid($_db_migration_to);
//===========================================================================================



$migrations->Run('01_test.php', <<<'DB_UPDATE_FILE'
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

$migrator = new Claromentis\Orm\Migrator();

$migrator->up(function(Doctrine\DBAL\Schema\Schema $schema) {
	$table = $schema->createTable('test');
	
	$table->addColumn('id', 'integer');
	$table->setPrimaryKey(['id']);
});
DB_UPDATE_FILE
);

//===========================================================================================
$migrations->SetVersion('01.01');