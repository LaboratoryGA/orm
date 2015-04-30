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
 * This class helps construct access to the database
 *
 * @author Nathan Crause <nathan at crause.name>
 */
final class Configuration {
	
	/**
	 * Singleton instance of this class
	 * 
	 * @var Configuration
	 */
	private static $instance;
	
	/**
	 * Retrieves singleton instance, or creates a new instance.
	 * 
	 * @return Configuration
	 */
	public static function get() {
		return self::$instance = self::$instance ?: new self();
	}
	
	private function __construct() {
		global $cfg_db_type, $cfg_db_host, $cfg_db_name, $cfg_db_user, 
				$cfg_db_pass;
		
		$this->dataSource = new DataSource($cfg_db_type, $cfg_db_host, 
				$cfg_db_name, new Authentication($cfg_db_user, $cfg_db_pass));
	}
	
	/**
	 * @var DataSource
	 */
	private $dataSource;
	
	public function getDataSource() {
		return $this->dataSource;
	}
	
}
