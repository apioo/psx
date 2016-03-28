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

namespace PSX\Data\Tests;

use PSX\Data\VisitorAbstract;

/**
 * VisitorAbstractTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class VisitorAbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testVisitor()
    {
        // the abstract visitor exists so that visitor implementations must not
        // implement every method. We simply test that we can call each method
        // of the interface

        $visitor = new FooVisitor();

        $visitor->visitObjectStart('foo');
        $visitor->visitObjectEnd();
        $visitor->visitObjectValueStart('bar', 'test');
        $visitor->visitObjectValueEnd();
        $visitor->visitArrayStart();
        $visitor->visitArrayEnd();
        $visitor->visitArrayValueStart('foo');
        $visitor->visitArrayValueEnd();
        $visitor->visitValue('foo');
    }
}

class FooVisitor extends VisitorAbstract
{
}
