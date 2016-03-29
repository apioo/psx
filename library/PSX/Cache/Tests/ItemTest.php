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

namespace PSX\Cache\Tests;

use DateInterval;
use DateTime;
use PSX\Cache\Item;

/**
 * ItemTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
    public function testExpiresAtInteger()
    {
        $item    = new Item('key', null, false);
        $expires = time() + 2;

        $this->assertEquals(0, $item->getTtl());

        $item->expiresAt($expires);

        $this->assertEquals(2, $item->getTtl());
    }

    public function testExpiresAtDateTime()
    {
        $item    = new Item('key', null, false);
        $expires = new DateTime('+2 seconds');

        $this->assertEquals(0, $item->getTtl());

        $item->expiresAt($expires);

        $this->assertEquals(2, $item->getTtl());
    }

    public function testExpiresAtNull()
    {
        $item = new Item('key', null, false);

        $this->assertEquals(0, $item->getTtl());

        $item->expiresAt(null);

        $this->assertEquals(0, $item->getTtl());
    }

    /**
     * @expectedException \PSX\Cache\Exception
     */
    public function testExpiresAtInvalidType()
    {
        $item = new Item('key', null, false);

        $this->assertEquals(0, $item->getTtl());

        $item->expiresAt('foo');
    }

    public function testExpiresAfterInteger()
    {
        $item    = new Item('key', null, false);
        $expires = 2;

        $this->assertEquals(0, $item->getTtl());

        $item->expiresAfter($expires);

        $this->assertEquals(2, $item->getTtl());
    }

    public function testExpiresAfterDateInterval()
    {
        $item    = new Item('key', null, false);
        $expires = new DateInterval('PT2S');

        $this->assertEquals(0, $item->getTtl());

        $item->expiresAfter($expires);

        $this->assertEquals(2, $item->getTtl());
    }

    public function testExpiresAfterNull()
    {
        $item = new Item('key', null, false);

        $this->assertEquals(0, $item->getTtl());

        $item->expiresAfter(null);

        $this->assertEquals(0, $item->getTtl());
    }

    /**
     * @expectedException \PSX\Cache\Exception
     */
    public function testExpiresAfterInvalidType()
    {
        $item = new Item('key', null, false);

        $this->assertEquals(0, $item->getTtl());

        $item->expiresAfter('foo');
    }
}
