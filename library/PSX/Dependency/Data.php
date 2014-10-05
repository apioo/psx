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

namespace PSX\Dependency;

use PSX\Data\Extractor;
use PSX\Data\Importer as DataImporter;
use PSX\Data\Reader;
use PSX\Data\ReaderFactory;
use PSX\Data\Record\FactoryFactory;
use PSX\Data\Record\Importer;
use PSX\Data\Record\ImporterManager;
use PSX\Data\Schema\Assimilator;
use PSX\Data\Schema\SchemaManager;
use PSX\Data\Schema\Validator;
use PSX\Data\Transformer;
use PSX\Data\Transformer\TransformerManager;
use PSX\Data\Writer;
use PSX\Data\WriterFactory;

/**
 * Data
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
trait Data
{
	/**
	 * @return PSX\Data\ReaderFactory
	 */
	public function getReaderFactory()
	{
		$reader = new ReaderFactory();
		$reader->addReader(new Reader\Json());
		$reader->addReader(new Reader\Form());
		$reader->addReader(new Reader\Xml());

		return $reader;
	}

	/**
	 * @return PSX\Data\WriterFactory
	 */
	public function getWriterFactory()
	{
		$writer = new WriterFactory();
		$writer->addWriter(new Writer\Json());
		$writer->addWriter(new Writer\Html($this->get('template'), $this->get('reverse_router')));
		$writer->addWriter(new Writer\Atom());
		$writer->addWriter(new Writer\Form());
		$writer->addWriter(new Writer\Jsonp());
		$writer->addWriter(new Writer\Rss());
		$writer->addWriter(new Writer\Soap());
		$writer->addWriter(new Writer\Xml());

		return $writer;
	}

	/**
	 * @return PSX\Data\Transformer\TransformerManager
	 */
	public function getTransformerManager()
	{
		$manager = new TransformerManager();
		$manager->addTransformer(new Transformer\Atom());
		$manager->addTransformer(new Transformer\Rss());
		$manager->addTransformer(new Transformer\Soap());
		$manager->addTransformer(new Transformer\XmlArray());

		return $manager;
	}

	/**
	 * @return PSX\Data\Record\ImporterManager
	 */
	public function getImporterManager()
	{
		$manager = new ImporterManager();
		$manager->addImporter(new Importer\Record($this->get('record_factory_factory')));
		$manager->addImporter(new Importer\Schema($this->get('schema_validator'), $this->get('record_factory_factory')));
		$manager->addImporter(new Importer\Table());

		return $manager;
	}

	/**
	 * @return PSX\Data\Schema\SchemaManagerInterface
	 */
	public function getSchemaManager()
	{
		return new SchemaManager();
	}

	/**
	 * @return PSX\Data\Schema\ValidatorInterface
	 */
	public function getSchemaValidator()
	{
		return new Validator();
	}

	/**
	 * @return PSX\Data\Schema\Assimilator
	 */
	public function getSchemaAssimilator()
	{
		return new Assimilator();
	}

	/**
	 * @return PSX\Data\Record\FactoryFactory
	 */
	public function getRecordFactoryFactory()
	{
		return new FactoryFactory();
	}

	/**
	 * @return PSX\Data\Importer
	 */
	public function getImporter()
	{
		return new DataImporter($this->get('extractor'), $this->get('importer_manager'));
	}

	/**
	 * @return PSX\Data\Extractor
	 */
	public function getExtractor()
	{
		return new Extractor($this->get('reader_factory'), $this->get('transformer_manager'));
	}
}
