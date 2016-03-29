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

namespace PSX\Framework\Console;

use PSX\Api\Resource;
use PSX\Api\Generator;
use PSX\Api\ListingInterface;
use PSX\Data\ExporterInterface;
use PSX\Framework\Config\Config;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * ResourceCommand
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResourceCommand extends Command
{
    protected $config;
    protected $resourceListing;
    protected $exporter;

    public function __construct(Config $config, ListingInterface $resourceListing, ExporterInterface $exporter)
    {
        parent::__construct();

        $this->config          = $config;
        $this->resourceListing = $resourceListing;
        $this->exporter        = $exporter;
    }

    protected function configure()
    {
        $this
            ->setName('resource')
            ->setDescription('Prints the schema of a resource in a specific format')
            ->addArgument('path', InputArgument::REQUIRED, 'Path of the api')
            ->addArgument('format', InputArgument::OPTIONAL, 'The output format one of: jsonschema, php, raml, serialize, swagger, wsdl, xsd');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $resource = $this->resourceListing->getResource($input->getArgument('path'));

        if ($resource instanceof Resource) {
            switch ($input->getArgument('format')) {
                case 'php':
                    $response = var_export($resource, true);
                    break;

                case 'raml':
                    $path  = ltrim($resource->getPath(), '/');
                    $title = $resource->getTitle();

                    if (empty($title)) {
                        $title = str_replace(' ', '', ucwords(str_replace('/', ' ', $path)));
                    }

                    $baseUri         = $this->config['psx_url'] . '/' . $this->config['psx_dispatch'];
                    $targetNamespace = $this->config['psx_json_namespace'];

                    $generator = new Generator\Raml($title, 1, $baseUri, $targetNamespace);
                    $response  = $generator->generate($resource);
                    break;

                case 'serialize':
                    $response = serialize($resource);
                    break;

                case 'swagger':
                    $baseUri         = $this->config['psx_url'] . '/' . $this->config['psx_dispatch'];
                    $targetNamespace = $this->config['psx_json_namespace'];

                    $generator = new Generator\Swagger($this->exporter, 1, $baseUri, $targetNamespace);
                    $response  = $generator->generate($resource);
                    break;

                case 'wsdl':
                    $path  = ltrim($resource->getPath(), '/');
                    $title = $resource->getTitle();

                    if (empty($title)) {
                        $title = str_replace(' ', '', ucwords(str_replace('/', ' ', $path)));
                    }

                    $endpoint        = $this->config['psx_url'] . '/' . $this->config['psx_dispatch'] . $path;
                    $targetNamespace = $this->config['psx_soap_namespace'];

                    $generator = new Generator\Wsdl($title, $endpoint, $targetNamespace);
                    $response  = $generator->generate($resource);
                    break;

                case 'xsd':
                    $targetNamespace = $this->config['psx_soap_namespace'];

                    $generator = new Generator\Xsd($targetNamespace);
                    $response  = $generator->generate($resource);
                    break;

                default:
                case 'jsonschema':
                    $targetNamespace = $this->config['psx_json_namespace'];

                    $generator = new Generator\JsonSchema($targetNamespace);
                    $response  = $generator->generate($resource);
                    break;
            }

            $output->write($response);
        } else {
            throw new RuntimeException('Invalid resource');
        }
    }
}
