<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\ActivityStream;

use PSX\Data\SerializeTestAbstract;

/**
 * LanguageTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
