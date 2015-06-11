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

namespace PSX\Dependency;

use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializerBuilder;
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
use PSX\Data\Serializer;
use PSX\Data\Transformer;
use PSX\Data\Transformer\TransformerManager;
use PSX\Data\Writer;
use PSX\Data\WriterFactory;

/**
 * Data
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
		$reader->addReader(new Reader\Json(), 16);
		$reader->addReader(new Reader\Form(), 8);
		$reader->addReader(new Reader\Xml(), 0);

		return $reader;
	}

	/**
	 * @return PSX\Data\WriterFactory
	 */
	public function getWriterFactory()
	{
		$writer = new WriterFactory();
		$writer->addWriter(new Writer\Json(), 48);
		$writer->addWriter(new Writer\Html($this->get('template'), $this->get('reverse_router')), 40);
		$writer->addWriter(new Writer\Atom(), 32);
		$writer->addWriter(new Writer\Form(), 24);
		$writer->addWriter(new Writer\Jsonp(), 16);
		$writer->addWriter(new Writer\Jsonx(), 15);
		$writer->addWriter(new Writer\Soap($this->get('config')->get('psx_soap_namespace')), 8);
		$writer->addWriter(new Writer\Xml(), 0);

		return $writer;
	}

	/**
	 * @return PSX\Data\Transformer\TransformerManager
	 */
	public function getTransformerManager()
	{
		$manager = new TransformerManager();
		$manager->addTransformer(new Transformer\Atom(), 16);
		$manager->addTransformer(new Transformer\Jsonx(), 9);
		$manager->addTransformer(new Transformer\Soap($this->get('config')->get('psx_soap_namespace')), 8);
		$manager->addTransformer(new Transformer\XmlArray(), 0);

		return $manager;
	}

	/**
	 * @return PSX\Data\Record\ImporterManager
	 */
	public function getImporterManager()
	{
		$manager = new ImporterManager();
		$manager->addImporter(new Importer\Record($this->get('record_factory_factory')), 16);
		$manager->addImporter(new Importer\Schema($this->get('schema_assimilator')), 8);
		$manager->addImporter(new Importer\Table(), 0);

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
		return new Assimilator($this->get('record_factory_factory'));
	}

	/**
	 * @return PSX\Data\Record\FactoryFactory
	 */
	public function getRecordFactoryFactory()
	{
		return new FactoryFactory($this->get('object_builder'));
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

	/**
	 * @return PSX\Data\SerializerInterface
	 */
	public function getSerializer()
	{
		$propertyNamingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());

		$serializer = SerializerBuilder::create()
			->setCacheDir(PSX_PATH_CACHE)
			->setDebug($this->get('config')->get('psx_debug'))
			->setSerializationVisitor(Serializer\SerializationVisitor::NAME, new Serializer\SerializationVisitor($propertyNamingStrategy))
			->build();

		return new Serializer($serializer);
	}
}
