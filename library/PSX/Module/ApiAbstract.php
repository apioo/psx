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

namespace PSX\Module;

use PSX\Base;
use PSX\Data\NotFoundException;
use PSX\Data\RecordInterface;
use PSX\Data\Writer;
use PSX\Data\WriterFactory;
use PSX\Data\WriterInterface;
use PSX\ModuleAbstract;
use PSX\Sql;
use PSX\Sql\Condition;

/**
 * ApiAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class ApiAbstract extends ModuleAbstract
{
	/**
	 * Returns an condition object depending on the filter param
	 *
	 * @param string $dateColumn
	 * @return PSX\Sql\Condition
	 */
	protected function getRequestCondition($dateColumn = 'date')
	{
		$con          = new Condition();
		$params       = $this->getRequestParams();
		$filterBy     = $params['filterBy'];
		$filterOp     = $params['filterOp'];
		$filterValue  = $params['filterValue'];
		$updatedSince = $params['updatedSince'];

		if(!empty($filterBy) && !empty($filterOp) && !empty($filterValue))
		{
			switch($filterOp)
			{
				case 'contains':
					$con->add($filterBy, 'LIKE', '%' . $filterValue . '%');
					break;

				case 'equals':
					$con->add($filterBy, '=', $filterValue);
					break;

				case 'startsWith':
					$con->add($filterBy, 'LIKE', $filterValue . '%');
					break;

				case 'present':
					$con->add($filterBy, 'IS NOT', 'NULL', 'AND');
					$con->add($filterBy, 'NOT LIKE', '');
					break;
			}
		}

		if(!empty($updatedSince))
		{
			$datetime = new DateTime($updatedSince);

			$con->add($dateColumn, '>', $datetime->format(DateTime::SQL));
		}

		return $con;
	}

	/**
	 * Returns wich fetch mode should be used. For json and xml we can use 
	 * assoc because the writer can simply transform an array. For more complex
	 * formats like atom we need objects
	 *
	 * @return integer
	 */
	protected function getMode()
	{
		$format = isset($_GET['format']) ? $_GET['format'] : null;

		switch($format)
		{
			case 'atom':
			case 'rss':
				return Sql::FETCH_OBJECT;
				break;

			case 'json':
			case 'jsonp':
			case 'xml':
			default:
				return Sql::FETCH_ASSOC;
				break;
		}
	}

	/**
	 * Returns an associative array containing all available request parameters
	 *
	 * @return array
	 */
	protected function getRequestParams()
	{
		$params = array();
		$params['fields']       = null;
		$params['updatedSince'] = null;
		$params['count']        = null;
		$params['filterBy']     = null;
		$params['filterOp']     = null;
		$params['filterValue']  = null;
		$params['sortBy']       = null;
		$params['sortOrder']    = null;
		$params['startIndex']   = null;

		if(isset($_GET['fields']))
		{
			$rawFields = explode(',', $_GET['fields']);
			$fields    = array();

			foreach($rawFields as $field)
			{
				$field = trim($field);

				if(strlen($field) > 1 && strlen($field) < 32 && ctype_alnum($field))
				{
					$fields[] = $field;
				}
			}

			if(!empty($fields))
			{
				$params['fields'] = $fields;
			}
		}

		if(isset($_GET['updatedSince']) && strlen($_GET['updatedSince']) < 22)
		{
			$params['updatedSince'] = $_GET['updatedSince'];
		}

		if(isset($_GET['count']))
		{
			$params['count'] = intval($_GET['count']);
		}

		if(isset($_GET['filterBy']) && ctype_alnum($_GET['filterBy']) && strlen($_GET['filterBy']) < 32)
		{
			$params['filterBy'] = $_GET['filterBy'];
		}

		if(isset($_GET['filterOp']) && in_array($_GET['filterOp'], array('contains', 'equals', 'startsWith', 'present')))
		{
			$params['filterOp'] = $_GET['filterOp'];
		}

		if(isset($_GET['filterValue']) && strlen($_GET['filterValue']) < 128)
		{
			$params['filterValue'] = $_GET['filterValue'];
		}

		if(isset($_GET['sortBy']) && strlen($_GET['sortBy']) < 128)
		{
			$params['sortBy'] = $_GET['sortBy'];
		}

		if(isset($_GET['sortOrder']))
		{
			$sortOrder = strtolower($_GET['sortOrder']);

			switch($sortOrder)
			{
				case 'asc':
				case 'ascending':
					$params['sortOrder'] = Sql::SORT_ASC;
					break;

				case 'desc':
				case 'descending':
					$params['sortOrder'] = Sql::SORT_DESC;
					break;
			}
		}

		if(isset($_GET['startIndex']))
		{
			$params['startIndex'] = intval($_GET['startIndex']);
		}

		return $params;
	}

	/**
	 * Writes the $record with the writer $writerType or depending on the get 
	 * parameter format or of the mime type of the Accept header
	 *
	 * @param PSX\Data\RecordInterface $record
	 * @param string $writerType
	 * @param integer $code
	 * @return void
	 */
	protected function setResponse(RecordInterface $record, $writerType = null, $code = 200)
	{
		// set response code
		Base::setResponseCode($code);

		// find best writer type if not set
		if($writerType === null)
		{
			$formats = array(
				'atom'  => Writer\Atom::$mime,
				'form'  => Writer\Form::$mime,
				'json'  => Writer\Json::$mime,
				'rss'   => Writer\Rss::$mime,
				'xml'   => Writer\Xml::$mime,
				'jsonp' => Writer\Jsonp::$mime,
			);

			$format      = isset($_GET['format']) ? $_GET['format'] : null;
			$contentType = isset($formats[$format]) ? $formats[$format] : Base::getRequestHeader('Accept');

			$writer = $this->container->get('writerFactory')->getWriterByContentType($contentType);
		}
		else
		{
			$writer = $this->container->get('writerFactory')->getWriteByInstance($writerType);
		}

		if($writer === null)
		{
			$writer = $this->container->get('writerFactory')->getDefaultWriter();
		}

		if(!$writer instanceof WriterInterface)
		{
			throw new NotFoundException('Could not find fitting data writer');
		}

		$response = $writer->write($record);

		// send content type header if not sent
		if(!Base::hasHeaderSent('Content-Type'))
		{
			header('Content-Type: ' . $writer->getContentType());
		}

		// for iframe file uploads we need an text/html content type header even 
		// if we want serve json content. If all browsers support the FormData
		// api we can send file uploads per ajax but for now we use this hack.
		// Note do not rely on this param it will be removed as soon as possible
		if(isset($_GET['htmlMime']))
		{
			header('Content-Type: text/html');
		}

		echo $response;
	}
}

