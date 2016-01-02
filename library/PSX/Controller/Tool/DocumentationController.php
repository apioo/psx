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

namespace PSX\Controller\Tool;

use PSX\Api\DocumentationInterface;
use PSX\Api\Resource;
use PSX\Api\Resource\Generator;
use PSX\Controller\ApiAbstract;
use PSX\Data\Record;
use PSX\Data\Schema\Generator as SchemaGenerator;
use PSX\Exception;
use PSX\Http\Exception as HttpException;

/**
 * DocumentationController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DocumentationController extends ApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Api\Resource\ListingInterface
     */
    protected $resourceListing;

    /**
     * @Inject
     * @var \PSX\Loader\ReverseRouter
     */
    protected $reverseRouter;

    public function doIndex()
    {
        $this->setBody([
            'routings' => $this->getRoutings(),
            'links'    => [
                [
                    'rel'  => 'self',
                    'href' => $this->reverseRouter->getUrl(get_class($this) . '::doIndex'),
                ],
                [
                    'rel'  => 'detail',
                    'href' => $this->reverseRouter->getUrl(get_class($this) . '::doDetail', array('{version}', '{path}')),
                ],
                [
                    'rel'  => 'api',
                    'href' => $this->reverseRouter->getDispatchUrl(),
                ],
            ]
        ]);
    }

    public function doDetail()
    {
        $version = $this->getUriFragment('version');
        $path    = $this->getUriFragment('path') ?: '/';

        if (empty($version) || empty($path)) {
            throw new HttpException\BadRequestException('Version and path not provided');
        }

        $documentation = $this->resourceListing->getDocumentation($path);

        if (!$documentation instanceof DocumentationInterface) {
            throw new HttpException\InternalServerErrorException('Controller provides no documentation informations');
        }

        if ($version == '*') {
            $version  = $documentation->getLatestVersion();
            $resource = $documentation->getResource($version);
        } else {
            $resource = $documentation->getResource($version);
        }

        if ($resource instanceof Resource) {
            $generator = new Generator\JsonSchema($this->config['psx_json_namespace']);

            $api = [
                'path'        => $resource->getPath(),
                'version'     => $version,
                'status'      => $resource->getStatus(),
                'description' => $resource->getDescription(),
                'schema'      => $generator->toArray($resource),
            ];

            // available versions
            $resources = $documentation->getResources();

            krsort($resources);

            $versions = array();
            foreach ($resources as $key => $row) {
                $versions[] = new Record('version', [
                    'version' => $key,
                    'status'  => $row->getStatus(),
                ]);
            }

            $api['versions'] = $versions;

            // path parameters
            if ($resource->hasPathParameters()) {
                $api['pathParameters'] = '#/definitions/path';
            }

            // methods
            $methods = $resource->getMethods();
            $details = [];

            foreach ($methods as $method) {
                $data = new \stdClass();

                // description
                $description = $method->getDescription();
                if (!empty($description)) {
                    $data->description = $description;
                }

                // query parameters
                if ($method->hasQueryParameters()) {
                    $data->queryParameters = '#/definitions/' . $method->getName() . '-query';
                }

                // request
                if ($method->hasRequest()) {
                    $data->request = '#/definitions/' . $method->getName() . '-request';
                }

                // responses
                $responses = $method->getResponses();
                if (!empty($responses)) {
                    $resps = array();
                    foreach ($responses as $statusCode => $response) {
                        $resps[$statusCode] = '#/definitions/' . $method->getName() . '-' . $statusCode . '-response';
                    }

                    $data->responses = $resps;
                }

                $details[$method->getName()] = $data;
            }

            $api['methods'] = $details;

            // links
            $links = $this->getLinks($version, $resource->getPath());
            if (!empty($links)) {
                $api['links'] = $links;
            }

            $this->setBody($api);
        } else {
            throw new HttpException\BadRequestException('Invalid api version');
        }
    }

    protected function getRoutings()
    {
        $routings  = array();
        $resources = $this->resourceListing->getResourceIndex();

        foreach ($resources as $resource) {
            $routings[] = new Record('routing', [
                'path'    => $resource->getPath(),
                'methods' => $resource->getAllowedMethods(),
                'version' => '*',
            ]);
        }

        return $routings;
    }

    protected function getLinks($version, $path)
    {
        $path   = ltrim($path, '/');
        $result = [];

        $wsdlGeneratorPath = $this->reverseRouter->getAbsolutePath('PSX\Controller\Tool\WsdlGeneratorController', array('version' => $version, 'path' => $path));
        if ($wsdlGeneratorPath !== null) {
            $result[] = [
                'rel'  => 'wsdl',
                'href' => $wsdlGeneratorPath,
            ];
        }

        $swaggerGeneratorPath = $this->reverseRouter->getAbsolutePath('PSX\Controller\Tool\SwaggerGeneratorController::doDetail', array('version' => $version, 'path' => $path));
        if ($swaggerGeneratorPath !== null) {
            $result[] = [
                'rel'  => 'swagger',
                'href' => $swaggerGeneratorPath,
            ];
        }

        $ramlGeneratorPath = $this->reverseRouter->getAbsolutePath('PSX\Controller\Tool\RamlGeneratorController', array('version' => $version, 'path' => $path));
        if ($ramlGeneratorPath !== null) {
            $result[] = [
                'rel'  => 'raml',
                'href' => $ramlGeneratorPath,
            ];
        }

        return $result;
    }

    protected function getViewGenerators()
    {
        return array(
            'Schema' => new Generator\Html\Schema(new SchemaGenerator\Html()),
        );
    }
}
