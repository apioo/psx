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

use PSX\Validate\Filter\Alnum;
use PSX\Validate\Filter\Alpha;
use PSX\Validate\Filter\Collection;
use PSX\Validate\Filter\Sha1;

/**
 * CollectionTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CollectionTest extends FilterTestCase
{
    public function testFilter()
    {
        $filter = new Collection(array(new Alnum(), new Alpha()));

        $this->assertEquals(true, $filter->apply('foo'));
        $this->assertEquals(false, $filter->apply('foo123'));

        $filter = new Collection(array(new Alnum(), new Alpha(), new Sha1()));

        $this->assertEquals('0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33', $filter->apply('foo'));
        $this->assertEquals(false, $filter->apply('foo123'));

        // test error message
        $this->assertErrorMessage($filter->getErrorMessage());
    }
}
