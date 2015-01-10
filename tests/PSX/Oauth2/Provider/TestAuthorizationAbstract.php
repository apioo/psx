<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Oauth2\Provider;

/**
 * TestAuthorizationAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TestAuthorizationAbstract extends AuthorizationAbstract
{
	protected function hasGrant(AccessRequest $request)
	{
		// normally we must check whether the user is authenticated and if not
		// we must redirect them to an login form which redirects the user back
		// if the login was successful. In this case we use the get parameter
		// for testing purpose

		return $this->request->getUrl()->getParameter('has_grant');
	}

	protected function generateCode(AccessRequest $request)
	{
		// this code must be stored in an database so we can later check whether
		// the code was generated. In this case we use the get parameter for 
		// testing purpose

		return $this->request->getUrl()->getParameter('code');
	}
}
