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

namespace PSX\Framework\Tests\Util;

use PSX\Framework\Util\Annotation;

/**
 * AnnotationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class AnnotationTest extends \PHPUnit_Framework_TestCase
{
    public function testParseNormal()
    {
        $comment = <<<'DOC'
/**
 * foobar
 *
 * @param string $tableName
 * @return PSX\Sql\TableInterface
 */
DOC;
        $doc = Annotation::parse($comment);

        $this->assertEquals('string $tableName', $doc->getFirstAnnotation('param'));
        $this->assertEquals('PSX\Sql\TableInterface', $doc->getFirstAnnotation('return'));
        $this->assertEquals(array('string $tableName'), $doc->getAnnotation('param'));
        $this->assertTrue($doc->hasAnnotation('param'));
        $this->assertEquals(array('param' => array('string $tableName'), 'return' => array('PSX\Sql\TableInterface')), $doc->getAnnotations());
    }

    public function testParseDoctrine()
    {
        $comment = <<<'DOC'
/**
 * foobar
 *
 * @param string $tableName
 * @author foo(bar)
 * @Column(type="string")
 */
DOC;
        $doc = Annotation::parse($comment);

        $this->assertEquals('string $tableName', $doc->getFirstAnnotation('param'));
        $this->assertEquals('foo(bar)', $doc->getFirstAnnotation('author'));
        $this->assertEquals('(type="string")', $doc->getFirstAnnotation('Column'));
    }

    public function testParseDoctrineMultipleParameters()
    {
        $comment = <<<'DOC'
/**
 * foobar
 *
 * @Column(name="foobar", type="string")
 */
DOC;
        $doc = Annotation::parse($comment);

        $this->assertEquals('(name="foobar", type="string")', $doc->getFirstAnnotation('Column'));
    }

    public function testParseMultiple()
    {
        $comment = <<<'DOC'
/**
 * foobar
 *
 * @param string $foo
 * @param string $bar
 * @return PSX\Sql\TableInterface
 */
DOC;
        $doc = Annotation::parse($comment);

        $this->assertEquals(array('string $foo', 'string $bar'), $doc->getAnnotation('param'));
        $this->assertEquals('PSX\Sql\TableInterface', $doc->getFirstAnnotation('return'));
    }

    public function testParseEmpty()
    {
        $comment = <<<'DOC'
/**
 * foobar
 *
 * @Id
 * @return PSX\Sql\TableInterface
 */
DOC;
        $doc = Annotation::parse($comment);

        $this->assertEquals(null, $doc->getFirstAnnotation('Id'));
        $this->assertEquals('PSX\Sql\TableInterface', $doc->getFirstAnnotation('return'));
    }

    public function testParseNamespaced()
    {
        $comment = <<<'DOC'
/**
 * foobar
 *
 * @ORM\Column(type="string")
 */
DOC;
        $doc = Annotation::parse($comment);

        $this->assertEquals('(type="string")', $doc->getFirstAnnotation('Column'));
    }

    public function testParseAttributes()
    {
        $attr = Annotation::parseAttributes('(type="string")');
        $this->assertEquals(array('type' => 'string'), $attr);

        $attr = Annotation::parseAttributes('(targetEntity="PSX\Data\Record\PersonEntity", inversedBy="news")');
        $this->assertEquals(array('targetEntity' => 'PSX\Data\Record\PersonEntity', 'inversedBy' => 'news'), $attr);
    }
}
