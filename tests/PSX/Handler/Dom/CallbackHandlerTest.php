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

namespace PSX\Handler\Dom;

use DOMDocument;
use PSX\Handler\MappingAbstract;
use PSX\Handler\HandlerTestCase;

/**
 * CallbackHandlerTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class CallbackHandlerTest extends \PHPUnit_Framework_TestCase
{
	use HandlerTestCase;

	protected function getHandler()
	{
		return new CallbackHandler(function(){
			$dom = new DOMDocument();
			$dom->loadXml($this->getXml());

			return new Mapping($dom, 'comments', 'comment', array(
				'id'     => MappingAbstract::TYPE_INTEGER | 10 | MappingAbstract::ID_PROPERTY,
				'userId' => MappingAbstract::TYPE_INTEGER | 10,
				'title'  => MappingAbstract::TYPE_STRING | 32,
				'date'   => MappingAbstract::TYPE_DATETIME,
			));
		});
	}

	protected function getXml()
	{
		return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<comments>
	<comment>
		<id>1</id>
		<userId>1</userId>
		<title>foo</title>
		<date>2013-04-29 16:56:32</date>
	</comment>
	<comment>
		<id>2</id>
		<userId>1</userId>
		<title>bar</title>
		<date>2013-04-29 16:56:32</date>
	</comment>
	<comment>
		<id>3</id>
		<userId>2</userId>
		<title>test</title>
		<date>2013-04-29 16:56:32</date>
	</comment>
	<comment>
		<id>4</id>
		<userId>3</userId>
		<title>blub</title>
		<date>2013-04-29 16:56:32</date>
	</comment>
</comments>
XML;
	}
}
