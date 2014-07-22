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

namespace PSX\Data\Schema;

use PSX\Data\Schema\Generator\TestSchema;

/**
 * ValidatorTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ValidatorTest extends \PHPUnit_Framework_TestCase
{
	public function testValidate()
	{
		$json = <<<'JSON'
{
	"tags": ["foo"],
	"receiver": [{
		"title": "bar"
	}],
	"read": true,
	"author": {
		"title": "test"
	},
	"sendDate": "2014-07-22",
	"readDate": "2014-07-22T22:47:00",
	"expires": "P1M",
	"price": 13.37,
	"rating": 4,
	"content": "foobar",
"question": "foo",
	"coffeeTime": "16:00:00"
}
JSON;

		$data = json_decode($json, true);

		$validator = new Validator();
		
		$this->assertTrue($validator->validate(new TestSchema(), $data));
	}
}
