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

namespace PSX\Framework\Controller\Tool;

use PSX\Api\Resource;
use PSX\Api\Generator;
use PSX\Framework\Controller\ApiAbstract;
use PSX\Data\Record;
use PSX\Schema\Generator as SchemaGenerator;
use PSX\Framework\Exception;
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
     * @var \PSX\Api\ListingInterface
     */
    protected $resourceListing;

    /**
     * @Inject
     * @var \PSX\Framework\Loader\ReverseRouter
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

        $resource = $this->resourceListing->getResource($path, $version);

        if ($resource instanceof Resource) {
            $generator = new Generator\JsonSchema($this->config['psx_json_namespace']);

            $api = new \stdClass();
            $api->path = $resource->getPath();
            $api->version = $version;
            $api->status = $resource->getStatus();
            $api->description = $resource->getDescription();
            $api->schema = $generator->toArray($resource);

            // path parameters
            if ($resource->hasPathParameters()) {
                $api->pathParameters = '#/definitions/path';
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

            $api->methods = $details;

            // links
            $links = $this->getLinks($version, $resource->getPath());
            if (!empty($links)) {
                $api->links = $links;
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

        $wsdlPath = $this->reverseRouter->getAbsolutePath('PSX\Framework\Controller\Generator\WsdlController', array('version' => $version, 'path' => $path));
        if ($wsdlPath !== null) {
            $result[] = [
                'rel'  => 'wsdl',
                'href' => $wsdlPath,
            ];
        }

        $swaggerPath = $this->reverseRouter->getAbsolutePath('PSX\Framework\Controller\Generator\SwaggerController::doDetail', array('version' => $version, 'path' => $path));
        if ($swaggerPath !== null) {
            $result[] = [
                'rel'  => 'swagger',
                'href' => $swaggerPath,
            ];
        }

        $ramlPath = $this->reverseRouter->getAbsolutePath('PSX\Framework\Controller\Generator\RamlController', array('version' => $version, 'path' => $path));
        if ($ramlPath !== null) {
            $result[] = [
                'rel'  => 'raml',
                'href' => $ramlPath,
            ];
        }

        return $result;
    }
}
