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

namespace PSX\Payment\Paypal;

use PSX\ControllerAbstract;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Data\RecordStore;
use PSX\Data\Record\DefaultImporter;
use PSX\Exception;
use PSX\Http;
use PSX\Http\PostRequest;
use PSX\Url;

/**
 * IpnAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class IpnAbstract extends ControllerAbstract
{
	const IPN_ENDPOINT = 'https://www.paypal.com/cgi-bin/webscr';

	/**
	 * @Inject
	 * @var PSX\Http
	 */
	protected $http;

	public function onPost()
	{
		// read request
		$data = array();
		$body = (string) $this->request->getBody();

		parse_str($body, $data);

		// import data
		$record = $this->getRecord($data);

		// verify request
		$url      = new Url(self::IPN_ENDPOINT);
		$request  = new PostRequest($url, array(), 'cmd=_notify-validate&' . $body);
		$response = $this->http->request($request);

		if($response->getStatusCode() == 200)
		{
			$body = (string) $response->getBody();

			if(strcmp($body, 'VERIFIED') === 0)
			{
				$this->onVerified($record);
			}
			else if(strcmp($body, 'INVALID') === 0)
			{
				$this->onInvalid($record);
			}
		}
	}

	protected function getRecord(array $data)
	{
		$result = array();

		foreach($data as $key => $value)
		{
			// convert to camelcase if underscore is in name
			if(strpos($key, '_') !== false)
			{
				$key = implode('', array_map('ucfirst', explode('_', $key)));
			}

			$result[lcfirst($key)] = $value;
		}

		return new Record('record', $result);
	}

	/**
	 * Is called if an transaction was verified. The record contains the 
	 * parameters from the request. Underscore values have an camelcase getter
	 *
	 * @param PSX\Data\RecordInterface $record
	 */
	abstract protected function onVerified(RecordInterface $record);

	/**
	 * Is called if an transaction was invalid. The record contains the 
	 * parameters from the request. Underscore values have an camelcase getter
	 *
	 * @param PSX\Data\RecordInterface $record
	 */
	abstract protected function onInvalid(RecordInterface $record);
}
