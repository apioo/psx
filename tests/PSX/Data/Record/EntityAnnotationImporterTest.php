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

namespace PSX\Data\Record;

use PSX\Data\Record;
use PSX\Data\RecordAbstract;
use PSX\Data\FactoryInterface;
use PSX\Data\BuilderInterface;

/**
 * EntityAnnotationImporterTest
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class EntityAnnotationImporterTest extends ImporterTestCase
{
	protected function getImporter()
	{
		return new EntityAnnotationImporter();
	}

	protected function getRecord()
	{
		return new NewsEntity();
	}
}

/**
 * @Entity
 * @Table(name="news")
 */
class NewsEntity
{
	/**
	 * @Column(type="integer")
	 */
	protected $id;

	/**
	 * @Column(type="string")
	 */
	protected $title;

	/**
	 * @Column(type="boolean")
	 */
	protected $active;

	/**
	 * @Column(type="boolean")
	 */
	protected $disabled;

	/**
	 * @Column(type="integer")
	 */
	protected $count;

	/**
	 * @Column(type="float")
	 */
	protected $rating;

	/**
	 * @Column(type="datetime")
	 */
	protected $date;

	/**
	 * @ManyToOne(targetEntity="PSX\Data\Record\PersonEntity", inversedBy="news")
	 * @JoinColumn(name="person_id", referencedColumnName="id")
	 */
	protected $person;

	/**
	 * @OneToMany(targetEntity="PSX\Data\Record\TagEntity", mappedBy="news")
	 */
	protected $tags;

	/**
	 * @OneToMany(targetEntity="PSX\Data\Record\AchievmentEntity", mappedBy="news")
	 * @DataFactory PSX\Data\Record\AchievmentEntityFactory
	 */
	protected $achievment;

	/**
	 * @ManyToOne(targetEntity="PSX\Data\Record\PaymentEntity", inversedBy="news")
	 * @JoinColumn(name="payment_id", referencedColumnName="id")
	 * @DataBuilder PSX\Data\Record\PaymentEntityBuilder
	 */
	protected $payment;
}

/**
 * @Entity
 * @Table(name="person")
 */
class PersonEntity
{
	/**
	 * @Column(type="string")
	 */
	protected $title;
}

/**
 * @Entity
 * @Table(name="tag")
 */
class TagEntity
{
	/**
	 * @Column(type="string")
	 */
	protected $title;
}

class AchievmentEntityFactory implements FactoryInterface
{
	public function factory($data)
	{
		if(isset($data['type']))
		{
			$class = 'PSX\Data\Record\AchievmentEntity' . ucfirst($data['type']);

			if(class_exists($class))
			{
				return new $class();
			}
		}

		return null;
	}
}

/**
 * @Entity
 * @Table(name="achievment")
 */
class AchievmentEntityFoo
{
	/**
	 * @Column(type="string")
	 */
	protected $type;

	/**
	 * @Column(type="string")
	 */
	protected $foo;
}

/**
 * @Entity
 * @Table(name="achievment")
 */
class AchievmentEntityBar
{
	/**
	 * @Column(type="string")
	 */
	protected $type;

	/**
	 * @Column(type="string")
	 */
	protected $bar;
}

class PaymentEntityBuilder implements BuilderInterface
{
	public function build($data)
	{
		// this is the place to build complex records depending on the content
		// if the default importer fits not your need

		return new Record('payment', array(
			'type'   => 'paypal',
			'custom' => 'foobar',
		));
	}
}

