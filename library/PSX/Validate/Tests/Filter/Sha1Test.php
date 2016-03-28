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

use PSX\Validate\Filter\Sha1;

/**
 * Sha1Test
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Sha1Test extends FilterTestCase
{
    public function testFilter()
    {
        $filter = new Sha1();

        $this->assertEquals('da39a3ee5e6b4b0d3255bfef95601890afd80709', $filter->apply(''));
        $this->assertEquals('d8e8ece39c437e515aa8997c1a1e94f1ed2a0e62', $filter->apply('Frank jagt im komplett verwahrlosten Taxi quer durch Bayern'));

        // test error message
        $this->assertErrorMessage($filter->getErrorMessage());
    }
}
