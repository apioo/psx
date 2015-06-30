<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Data\Record\Visitor;

use DateTime;
use PSX\Data\Record;
use PSX\Uri;

/**
 * ImporterTestCase
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class VisitorTestCase extends \PHPUnit_Framework_TestCase
{
	protected function getRecord()
	{
		return new Record('record', array(
			'id' => 1,
			'title' => 'foobar',
			'active' => true,
			'disabled' => false,
			'rating' => 12.45,
			'age' => null,
			'date' => new DateTime('2014-01-01T12:34:47+01:00'),
			'href' => new Uri('http://foo.com'),
			'person' => new Record('person', array(
				'title' => 'Foo',
			)),
			'category' => new Record('category', array(
				'general' => new Record('category', array(
					'news' => new Record('category', array(
						'technic' => 'Foo',
					)),
				)),
			)),
			'tags' => array('bar', 'foo', 'test'),
			'entry' => array(
				new Record('entry', array(
					'title' => 'bar'
				)),
				new Record('entry', array(
					'title' => 'foo'
				)),
			),
		));
	}
}
