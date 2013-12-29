<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Util;

/**
 * AnnotationTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class AnnotationTest extends \PHPUnit_Framework_TestCase
{
	protected function setUp()
	{
	}

	protected function tearDown()
	{
	}

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
}
