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

namespace PSX\Data\Writer;

use PSX\Data\WriterTestCase;
use PSX\DateTime;

/**
 * JasTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class JasTest extends WriterTestCase
{
	public function testWrite()
	{
		ob_start();

		$writer = new Jas();
		$writer->write($this->getRecord());

		$actual = ob_get_contents();

		ob_end_clean();


		$expect = <<<TEXT
{
  "actor":{
    "displayName":"foo",
    "objectType":"person"
  },
  "object":{
    "content":"foobar",
    "displayName":"bar",
    "id":1,
    "objectType":"article",
    "published":"2012-03-11T13:37:21+00:00"
  },
  "verb":"post"
}
TEXT;

		$this->assertJsonStringEqualsJsonString($expect, $actual);
	}

	public function testWriteResultSet()
	{
		ob_start();

		$writer = new Jas();
		$writer->write($this->getResultSet());

		$actual = ob_get_contents();

		ob_end_clean();


		$expect = <<<TEXT
{
  "itemsPerPage":8,
  "startIndex":0,
  "items":[{
    "actor":{
      "displayName":"foo",
      "objectType":"person"
    },
    "object":{
      "content":"foobar",
      "displayName":"bar",
      "id":1,
      "objectType":"article",
      "published":"2012-03-11T13:37:21+00:00"
    },
    "verb":"post"
  },{
    "actor":{
      "displayName":"foo",
      "objectType":"person"
    },
    "object":{
      "content":"foobar",
      "displayName":"bar",
      "id":2,
      "objectType":"article",
      "published":"2012-03-11T13:37:21+00:00"
    },
    "verb":"post"
  }]
}
TEXT;

		$this->assertJsonStringEqualsJsonString($expect, $actual);
	}
}
