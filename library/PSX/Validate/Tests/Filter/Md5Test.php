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

use PSX\Validate\Filter\Md5;

/**
 * Md5Test
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Md5Test extends FilterTestCase
{
    public function testFilter()
    {
        $filter = new Md5();

        $this->assertEquals('d41d8cd98f00b204e9800998ecf8427e', $filter->apply(''));
        $this->assertEquals('7e716d0e702df0505fc72e2b89467910', $filter->apply('Frank jagt im komplett verwahrlosten Taxi quer durch Bayern'));

        // test error message
        $this->assertErrorMessage($filter->getErrorMessage());
    }
}
