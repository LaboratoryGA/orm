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

use ClaCache;
use Doctrine\Common\Cache\CacheProvider;

/**
 * Cuatom Doctrine cache implementation which uses ClaCache as the backend.
 *
 * @author Nathan Crause <nathan at crause.name>
 */
class Cache extends CacheProvider {
	
//	public function __construct() {
//		global $APPDATA;
//		
//		$this->log = fopen($APPDATA . '/orm/log', 'a');
//	}
//	
//	public function __destruct() {
//		fclose($this->log);
//	}
	
	private function getKey($id) {
		return "ORM::$id";
	}

	protected function doContains($id) {
//		fwrite($this->log, "Testing existance of $id\r\n");
		
		return !!ClaCache::GetGlobal($this->getKey($id));
	}

	protected function doDelete($id) {
//		fwrite($this->log, "Deleting $id\r\n");
		
		ClaCache::DeleteGlobal($this->getKey($id));
		
		return true;
	}

	protected function doFetch($id) {
//		fwrite($this->log, "Fetching $id\r\n");
		
		$result = ClaCache::GetGlobal($this->getKey($id));
		
//		fwrite($this->log, 'Fetched: ' . print_r($result, true) . "\r\n");
		
		if (is_null($result)) {
			return false;
		}
		
		return $result;
	}

	protected function doFlush() {
//		fwrite($this->log, "Flushing\r\n");
		
		ClaCache::ClearGlobal('ORM');
		
		return true;
	}

	protected function doGetStats() {
		return null;
	}

	protected function doSave($id, $data, $lifeTime = false) {
//		fwrite($this->log, "Saving $id\r\n");
		
		ClaCache::SetGlobal($this->getKey($id), $data, $lifeTime);
		
		return true;
	}

}
