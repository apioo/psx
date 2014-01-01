<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

use InvalidArgumentException;
use PSX\Data\InvalidDataException;
use PSX\Data\Record\ImporterInterface;
use PSX\Http\Message;

/**
 * ResImporter
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ResImporter implements ImporterInterface
{
	public function import($record, $data)
	{
		if(!is_array($data))
		{
			throw new InvalidArgumentException('Data must be an array');
		}

		$record->setParams($data);

		if(isset($data['openid_op_endpoint']))
		{
			$record->setOpEndpoint($data['openid_op_endpoint']);
		}
		else
		{
			throw new InvalidDataException('OP endpoint not set');
		}

		if(isset($data['openid_claimed_id']))
		{
			$record->setClaimedId($data['openid_claimed_id']);
		}

		if(isset($data['openid_identity']))
		{
			$record->setIdentity($data['openid_identity']);
		}

		if(isset($data['openid_return_to']))
		{
			$record->setReturnTo($data['openid_return_to']);
		}
		else
		{
			throw new InvalidDataException('Return to not set');
		}

		if(isset($data['openid_response_nonce']))
		{
			$record->setResponseNonce($data['openid_response_nonce']);
		}
		else
		{
			throw new InvalidDataException('Response nonce not set');
		}

		if(isset($data['openid_invalidate_handle']))
		{
			$record->setInvalidateHandle($data['openid_invalidate_handle']);
		}

		if(isset($data['openid_assoc_handle']))
		{
			$record->setAssocHandle($data['openid_assoc_handle']);
		}
		else
		{
			throw new InvalidDataException('Assoc handle not set');
		}

		if(isset($data['openid_signed']))
		{
			$record->setSigned($data['openid_signed']);
		}
		else
		{
			throw new InvalidDataException('Signed not set');
		}

		if(isset($data['openid_sig']))
		{
			$record->setSig($data['openid_sig']);
		}
		else
		{
			throw new InvalidDataException('Sig not set');
		}
	}
}
