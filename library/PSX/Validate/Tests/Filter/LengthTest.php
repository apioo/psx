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

use PSX\Validate\Filter\Length;

/**
 * LengthTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class LengthTest extends FilterTestCase
{
    public function testFilterIntegerLength()
    {
        $filter = new Length(3, 8);

        $this->assertEquals(false, $filter->apply(2));
        $this->assertEquals(true, $filter->apply(3));
        $this->assertEquals(true, $filter->apply(8));
        $this->assertEquals(false, $filter->apply(9));

        // test error message
        $this->assertErrorMessage($filter->getErrorMessage());

        $filter = new Length(8);

        $this->assertEquals(true, $filter->apply(2));
        $this->assertEquals(true, $filter->apply(3));
        $this->assertEquals(true, $filter->apply(8));
        $this->assertEquals(false, $filter->apply(9));

        // test error message
        $this->assertErrorMessage($filter->getErrorMessage());
    }

    public function testFilterFloatLength()
    {
        $filter = new Length(3.4, 8.4);

        $this->assertEquals(false, $filter->apply(3.3));
        $this->assertEquals(true, $filter->apply(3.4));
        $this->assertEquals(true, $filter->apply(8.4));
        $this->assertEquals(false, $filter->apply(8.5));

        // test error message
        $this->assertErrorMessage($filter->getErrorMessage());

        $filter = new Length(8.4);

        $this->assertEquals(true, $filter->apply(3.3));
        $this->assertEquals(true, $filter->apply(3.4));
        $this->assertEquals(true, $filter->apply(8.4));
        $this->assertEquals(false, $filter->apply(8.5));

        // test error message
        $this->assertErrorMessage($filter->getErrorMessage());
    }

    public function testFilterArrayLength()
    {
        $filter = new Length(3, 8);

        $this->assertEquals(false, $filter->apply(range(0, 1)));
        $this->assertEquals(true, $filter->apply(range(0, 2)));
        $this->assertEquals(true, $filter->apply(range(0, 7)));
        $this->assertEquals(false, $filter->apply(range(0, 8)));

        // test error message
        $this->assertErrorMessage($filter->getErrorMessage());

        $filter = new Length(8);

        $this->assertEquals(true, $filter->apply(range(0, 1)));
        $this->assertEquals(true, $filter->apply(range(0, 2)));
        $this->assertEquals(true, $filter->apply(range(0, 7)));
        $this->assertEquals(false, $filter->apply(range(0, 8)));

        // test error message
        $this->assertErrorMessage($filter->getErrorMessage());
    }

    public function testFilterStringLength()
    {
        $filter = new Length(3, 8);

        $this->assertEquals(false, $filter->apply('fo'));
        $this->assertEquals(true, $filter->apply('foo'));
        $this->assertEquals(true, $filter->apply('foobarte'));
        $this->assertEquals(false, $filter->apply('foobartes'));

        // test error message
        $this->assertErrorMessage($filter->getErrorMessage());

        $filter = new Length(8);

        $this->assertEquals(true, $filter->apply('fo'));
        $this->assertEquals(true, $filter->apply('foo'));
        $this->assertEquals(true, $filter->apply('foobarte'));
        $this->assertEquals(false, $filter->apply('foobartes'));

        // test error message
        $this->assertErrorMessage($filter->getErrorMessage());
    }
}
