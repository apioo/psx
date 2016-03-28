<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Validate\Tests\Filter;

use PSX\Validate\Filter\DateTime;

/**
 * DateTimeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DateTimeTest extends FilterTestCase
{
    public function testFilter()
    {
        $filter = new DateTime();
        $expect = new \DateTime('2010-10-10 00:00:00');

        $this->assertInstanceOf('DateTime', $filter->apply($expect));
        $this->assertEquals($expect, $filter->apply($expect));
        $this->assertEquals($expect, $filter->apply('10.10.2010'));
        $this->assertEquals(false, $filter->apply('foobar'));

        // test error message
        $this->assertErrorMessage($filter->getErrorMessage());
    }

    public function testFilterFormat()
    {
        $filter = new DateTime('Y-m-d H:i:s');
        $expect = new \DateTime('2010-10-10 00:00:00');

        $this->assertEquals('2010-10-10 00:00:00', $filter->apply($expect));
        $this->assertEquals('2010-10-10 00:00:00', $filter->apply('10.10.2010'));
        $this->assertEquals(false, $filter->apply('foobar'));

        // test error message
        $this->assertErrorMessage($filter->getErrorMessage());
    }
}
