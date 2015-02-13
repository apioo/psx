<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\ActivityStream;

use DateTime;
use PSX\ActivityStream\ObjectType\Binary;
use PSX\Data\SerializeTestAbstract;

/**
 * LanguageTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class LanguageTest extends SerializeTestAbstract
{
	public function testLang()
	{
		$value    = 'lorem ipsum';
		$language = new Language($value);

		$this->assertEquals($value, $language->getValue());
		$this->assertEquals($value, $language->getPreferred());
		$this->assertEquals($value, $language->__toString());
	}

	public function testLangWithoutValue()
	{
		$value    = null;
		$language = new Language($value);

		$this->assertEquals($value, $language->getValue());
		$this->assertEquals($value, $language->getPreferred());
		$this->assertEquals($value, $language->__toString());
	}

	public function testMultipleLang()
	{
		$value    = array('en' => 'en lorem ipsum', 'de' => 'de lorem ipsum');
		$language = new Language($value);

		$this->assertEquals($value, $language->getValue());
		$this->assertEquals('en lorem ipsum', $language->getPreferred());
		$this->assertEquals('en lorem ipsum', $language->__toString());
	}

	public function testMultipleLangWithoutPreferred()
	{
		$value    = array('de' => 'de lorem ipsum');
		$language = new Language($value);

		$this->assertEquals($value, $language->getValue());
		$this->assertEquals('de lorem ipsum', $language->getPreferred());
		$this->assertEquals('de lorem ipsum', $language->__toString());
	}

	public function testMultipleLangWithoutValue()
	{
		$value    = array();
		$language = new Language($value);

		$this->assertEquals($value, $language->getValue());
		$this->assertEquals(null, $language->getPreferred());
		$this->assertEquals('', $language->__toString());
	}
}
