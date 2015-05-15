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

namespace PSX\Console\Schema;

use PSX\Api\DocumentationInterface;
use PSX\Api\Resource;
use PSX\Api\Resource\ListingInterface;
use PSX\Api\Resource\Generator;
use PSX\Config;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * XsdCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class XsdCommand extends Command
{
	protected $config;
	protected $resourceListing;

	public function __construct(Config $config, ListingInterface $resourceListing)
	{
		parent::__construct();

		$this->config          = $config;
		$this->resourceListing = $resourceListing;
	}

	protected function configure()
	{
		$this
			->setName('schema:xsd')
			->setDescription('Prints the XSD schema of an API')
			->addArgument('path', InputArgument::REQUIRED, 'Path of the schema api')
			->addArgument('version', InputArgument::OPTIONAL, 'Version of the api');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$version = $input->getArgument('version') ?: '*';
		$path    = $input->getArgument('path');
		$doc     = $this->resourceListing->getDocumentation($path);

		if($doc instanceof DocumentationInterface)
		{
			if($version == '*')
			{
				$version = $doc->getLatestVersion();
			}

			$resource = $doc->getResource($version);

			if(!$resource instanceof Resource)
			{
				throw new RuntimeException('Given version is not available');
			}

			$targetNamespace = $this->config['psx_soap_namespace'];

			$generator = new Generator\Xsd($targetNamespace);

			$output->write($generator->generate($resource));
		}
		else
		{
			throw new RuntimeException('Invalid resource');
		}
	}
}
