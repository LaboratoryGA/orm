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

namespace Claromentis\Orm\Components;

use TemplaterComponent;

/**
 * Description of CurrentUser
 *
 * @author Nathan Crause <nathan at crause.name>
 */
class CurrentUser implements TemplaterComponent {
	
	public function Show($attributes) {
		\ClaApplication::Enter('orm');
		
//		$user = \Claromentis\Orm\Models\User::find($_SESSION['SESSION_UID']);
		$user = \Claromentis\Orm\EntityManagerFactory::get()->find('Claromentis\Orm\Models\User', $_SESSION['SESSION_UID']);
//		$user = \Claromentis\Orm\EntityManagerFactory::get()->getRepository('Claromentis\Orm\Models\User')->find($_SESSION['SESSION_UID']);
		
		return <<<__HTML
<ul>
	<li>Subject: {$user->getSubject()}</li>
	<li>Full Name: {$user->getFullName()}</li>
</ul>
__HTML;
	}

}
