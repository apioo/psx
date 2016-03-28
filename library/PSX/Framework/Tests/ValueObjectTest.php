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

namespace PSX\Framework\Tests;

use PSX\DateTime\Date;
use PSX\DateTime\DateTime;
use PSX\DateTime\Time;
use PSX\Http\Cookie;
use PSX\Http\MediaType;
use PSX\Uri\Uri;
use PSX\Uri\Url;
use PSX\Uri\Urn;

/**
 * ValueObjectTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ValueObjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * This test ensures that the toString method of an value object creates an
     * string representation containing all components from the state. And that
     * this string can be parsed by the same object without lossing any
     * information
     */
    public function testObject()
    {
        $objects = $this->getObjects();

        foreach ($objects as $object) {
            $voClass   = get_class($object);
            $newObject = new $voClass($object->toString());

            $this->assertEquals($object->toString(), $newObject->toString());
            $this->assertEquals($object->toString(), (string) $newObject);
        }
    }

    protected function getObjects()
    {
        return [
            new Uri('foo://user:password@example.com:8042/over/there?name=ferret&foo=bar#nose'),
            new Urn('urn:uuid:f81d4fae-7dec-11d0-a765-00a0c91e6bf6'),
            new Url('http://benutzername:passwort@hostname:8080/pfad?argument=wert#textanker'),
            new MediaType('text/plain; q=0.5'),
            new Cookie('DNR=deleted; expires=Tue, 24-Dec-2013 11:39:14 GMT; path=/; domain=.www.yahoo.com'),
            new Date('2015-04-25'),
            new Time('19:35:20.1234+01:00'),
            new DateTime('2015-04-25T19:35:20.1234+01:00'),
        ];
    }
}
