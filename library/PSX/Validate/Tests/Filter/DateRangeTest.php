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

use PSX\Validate\Filter\DateRange;

/**
 * DateRangeTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DateRangeTest extends FilterTestCase
{
    public function testFilter()
    {
        $from   = new \DateTime('2010-10-10 06:00:00');
        $to     = new \DateTime('2010-10-12 08:30:00');
        $filter = new DateRange($from, $to);

        // test from
        $this->assertEquals(false, $filter->apply('2010-10-10 05:59:59'));
        $this->assertEquals(new \DateTime('2010-10-10 06:00:00'), $filter->apply('2010-10-10 06:00:00'));
        $this->assertEquals(new \DateTime('2010-10-10 06:00:01'), $filter->apply('2010-10-10 06:00:01'));

        // test between
        $this->assertEquals(new \DateTime('2010-10-11 06:00:00'), $filter->apply('2010-10-11 06:00:00'));

        // test to
        $this->assertEquals(new \DateTime('2010-10-12 08:29:59'), $filter->apply('2010-10-12 08:29:59'));
        $this->assertEquals(new \DateTime('2010-10-12 08:30:00'), $filter->apply('2010-10-12 08:30:00'));
        $this->assertEquals(false, $filter->apply('2010-10-12 08:30:01'));

        // test error message
        $this->assertErrorMessage($filter->getErrorMessage());
    }

    public function testFilterFrom()
    {
        $from   = new \DateTime('2010-10-10 06:00:00');
        $filter = new DateRange($from);

        // test from
        $this->assertEquals(false, $filter->apply('2010-10-10 05:59:59'));
        $this->assertEquals(new \DateTime('2010-10-10 06:00:00'), $filter->apply('2010-10-10 06:00:00'));
        $this->assertEquals(new \DateTime('2010-10-10 06:00:01'), $filter->apply('2010-10-10 06:00:01'));

        // test between
        $this->assertEquals(new \DateTime('2010-10-11 06:00:00'), $filter->apply('2010-10-11 06:00:00'));

        // test error message
        $this->assertErrorMessage($filter->getErrorMessage());
    }

    public function testFilterTo()
    {
        $to     = new \DateTime('2010-10-12 08:30:00');
        $filter = new DateRange(null, $to);

        // test between
        $this->assertEquals(new \DateTime('2010-10-11 06:00:00'), $filter->apply('2010-10-11 06:00:00'));

        // test to
        $this->assertEquals(new \DateTime('2010-10-12 08:29:59'), $filter->apply('2010-10-12 08:29:59'));
        $this->assertEquals(new \DateTime('2010-10-12 08:30:00'), $filter->apply('2010-10-12 08:30:00'));
        $this->assertEquals(false, $filter->apply('2010-10-12 08:30:01'));

        // test error message
        $this->assertErrorMessage($filter->getErrorMessage());
    }

    public function testFilterInvalidValue()
    {
        $from   = new \DateTime('2010-10-10 06:00:00');
        $to     = new \DateTime('2010-10-12 08:30:00');
        $filter = new DateRange($from, $to);

        $this->assertFalse($filter->apply('foo'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEmptyConstructor()
    {
        new DateRange();
    }
}
