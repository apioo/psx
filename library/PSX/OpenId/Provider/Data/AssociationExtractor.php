<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\OpenId\Provider\Data;

use PSX\Data\InvalidDataException;

/**
 * AssociationExtractor
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class AssociationExtractor
{
	public function extract(array $data)
	{
		$record = new AssociationRequest();

		if(isset($data['openid_assoc_type']))
		{
			$record->setAssocType($data['openid_assoc_type']);
		}

		if(isset($data['openid_session_type']))
		{
			$record->setSessionType($data['openid_session_type']);
		}

		if(isset($data['openid_dh_modulus']))
		{
			$record->setDhModulus($data['openid_dh_modulus']);
		}

		if(isset($data['openid_dh_gen']))
		{
			$record->setDhGen($data['openid_dh_gen']);
		}

		if(isset($data['openid_dh_consumer_public']))
		{
			$record->setDhConsumerPublic($data['openid_dh_consumer_public']);
		}

		return $record;
	}
}
