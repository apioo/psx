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

namespace PSX\Data\Record;

use PSX\Data\Record;
use PSX\Data\RecordAbstract;
use PSX\Data\Record\Definition\Reader;
use PSX\Data\FactoryInterface;
use PSX\Data\BuilderInterface;

/**
 * DefinitionImporterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DefinitionImporterTest extends ImporterTestCase
{
	protected function getImporter()
	{
		$reader     = new Reader\XmlString();
		$collection = $reader->read($this->getDefinitionXml());

		return new DefinitionImporter($collection);
	}

	protected function getRecord()
	{
		return 'news';
	}

	protected function getDefinitionXml()
	{
		return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<definition>
	<record name="news">
		<property name="id" type="integer" />
		<property name="title" type="string" />
		<property name="active" type="boolean" />
		<property name="disabled" type="boolean" />
		<property name="count" type="integer" />
		<property name="rating" type="float" />
		<property name="date" type="string" class="DateTime" />
		<property name="person" type="object" reference="person" />
		<property name="tags" type="array">
			<property type="object" reference="tag" />
		</property>
		<property name="achievment" type="array">
			<property type="object" class="PSX\Data\Record\AchievmentFactory" />
		</property>
		<property name="payment" type="object" class="PSX\Data\Record\PaymentBuilder" />
	</record>
	<record name="person">
		<property name="title" type="string" />
	</record>
	<record name="tag">
		<property name="title" type="string" />
	</record>
	<record name="achievment_foo">
		<property name="type" type="string" />
		<property name="foo" type="string" />
	</record>
	<record name="achievment_bar">
		<property name="type" type="string" />
		<property name="bar" type="string" />
	</record>
</definition>
XML;
	}
}

class AchievmentFactory implements FactoryInterface
{
	public function factory($data)
	{
		if(isset($data['type']))
		{
			return 'achievment_' . $data['type'];
		}

		return null;
	}
}

class PaymentBuilder implements BuilderInterface
{
	public function build($data)
	{
		// this is the place to build complex records depending on the content
		// if the default importer fits not your need

		return new Record('payment', array(
			'type'   => 'paypal',
			'custom' => 'foobar'
		));
	}
}

