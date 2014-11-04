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

namespace PSX\Handler\Impl\Mongodb;

use DateTime;
use DOMDocument;
use DOMElement;
use MongoClient;
use PSX\Handler\MappingAbstract;

/**
 * MongodbTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class MongodbTestCase extends \PHPUnit_Framework_TestCase
{
	private static $client;

	public function setUp()
	{
		if(!class_exists('MongoClient'))
		{
			$this->markTestSkipped('Mongo client not installed');
		}

		parent::setUp();

		// insert dummy data
		$client = $this->getMongoClient();
		$db = $client->psx;

		$dom = new DOMDocument();
		$dom->load($this->getDataSetFlatXmlFile());

		$mapping = $this->getHandler()->getMapping()->getFields();
		$root    = $dom->getElementsByTagName('dataset')->item(0);
		$data    = array();
		for($i = 0; $i < $root->childNodes->length; $i++)
		{
			if($root->childNodes->item($i) instanceof DOMElement)
			{
				$element = $root->childNodes->item($i);
				$name    = $element->nodeName;

				if(!isset($data[$name]))
				{
					$data[$name] = array();
				}

				$row = array();
				foreach($element->attributes as $attr)
				{
					if(isset($mapping[$attr->name]))
					{
						$row[$attr->name] = $this->unserializeType($attr->value, $mapping[$attr->name]);
					}
				}

				if(!empty($row))
				{
					$data[$name][] = $row;
				}
			}
		}

		foreach($data as $name => $rows)
		{
			$db->dropCollection($name);

			$collection = $db->createCollection($name);

			foreach($rows as $row)
			{
				$collection->insert($row);
			}
		}
	}

	protected function getMongoClient()
	{
		if(self::$client === null)
		{
			// the default di container doesnt have the mongo client service
			self::$client = new MongoClient();
		}

		return self::$client;
	}

	/**
	 * @return string
	 */
	abstract public function getDataSetFlatXmlFile();

	protected function unserializeType($data, $type)
	{
		$type = (($type >> 20) & 0xFF) << 20;

		switch($type)
		{
			case MappingAbstract::TYPE_INTEGER:
				return (int) $data;
				break;

			case MappingAbstract::TYPE_FLOAT:
				return (float) $data;
				break;

			case MappingAbstract::TYPE_BOOLEAN:
				return (bool) $data;
				break;

			default:
			case MappingAbstract::TYPE_DATETIME:
			case MappingAbstract::TYPE_STRING:
				return (string) $data;
				break;
		}
	}
}
