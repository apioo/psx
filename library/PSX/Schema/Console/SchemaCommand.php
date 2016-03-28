<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Schema\Console;

use Doctrine\Common\Annotations\Reader;
use PSX\Schema\Parser;
use PSX\Schema\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * SchemaCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SchemaCommand extends Command
{
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $annotationReader;

    /**
     * @var string
     */
    protected $targetNamespace;

    public function __construct(Reader $annotationReader, $targetNamespace)
    {
        parent::__construct();

        $this->annotationReader = $annotationReader;
        $this->targetNamespace  = $targetNamespace;
    }

    protected function configure()
    {
        $this
            ->setName('schema')
            ->setDescription('Parses an arbitrary source and outputs the schema in a specific format')
            ->addArgument('parser', InputArgument::REQUIRED, 'The parser which should be used either "jsonschema" or "popo"')
            ->addArgument('source', InputArgument::REQUIRED, 'The schema source depending on the parser this is either a absolute class name or schema file')
            ->addArgument('format', InputArgument::OPTIONAL, 'Optional the output format possible values are: html, php, serialize, xsd, jsonschema');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        switch ($input->getArgument('parser')) {
            case 'jsonschema':
                $parser = Parser\JsonSchema::fromFile($input->getArgument('source'));
                break;

            case 'popo':
            default:
                $parser = new Parser\Popo($this->annotationReader);
                break;
        }

        $schema = $parser->parse($input->getArgument('source'));

        switch ($input->getArgument('format')) {
            case 'html':
                $generator = new Generator\Html();
                $response  = $generator->generate($schema);
                break;

            case 'php':
                $response = var_export($schema, true);
                break;

            case 'serialize':
                $response = serialize($schema);
                break;

            case 'xsd':
                $generator = new Generator\Xsd($this->targetNamespace);
                $response  = $generator->generate($schema);
                break;

            default:
            case 'jsonschema':
                $generator = new Generator\JsonSchema();
                $response  = $generator->generate($schema);
                break;
        }

        $output->write($response);
    }
}
