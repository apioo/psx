<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Swagger;

use PSX\Data\SerializeTestAbstract;

/**
 * InfoObjectTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class InfoObjectTest extends SerializeTestAbstract
{
	public function testSerialize()
	{
		$info = new InfoObject();
		$info->setTitle('Swagger Sample App');
		$info->setDescription('This is a sample server Petstore server');
		$info->setTermsOfServiceUrl('http://helloreverb.com/terms/');
		$info->setContact('apiteam@wordnik.com');
		$info->setLicense('Apache 2.0');
		$info->setLicenseUrl('http://www.apache.org/licenses/LICENSE-2.0.html');

		$content = <<<JSON
{
  "title": "Swagger Sample App",
  "description": "This is a sample server Petstore server",
  "termsOfServiceUrl": "http://helloreverb.com/terms/",
  "contact": "apiteam@wordnik.com",
  "license": "Apache 2.0",
  "licenseUrl": "http://www.apache.org/licenses/LICENSE-2.0.html"
}
JSON;

		$this->assertRecordEqualsContent($info, $content);
	}
}
