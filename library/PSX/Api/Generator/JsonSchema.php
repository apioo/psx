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

namespace PSX\Api\Generator;

use PSX\Api\Resource;
use PSX\Api\GeneratorAbstract;
use PSX\Json\Parser;
use PSX\Schema\Generator\JsonSchema as JsonSchemaGenerator;
use PSX\Schema\SchemaInterface;

/**
 * JsonSchema
 *
 * @see     http://tools.ietf.org/html/draft-zyp-json-schema-04
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class JsonSchema extends GeneratorAbstract
{
    protected $targetNamespace;

    public function __construct($targetNamespace)
    {
        $this->targetNamespace = $targetNamespace;
    }

    public function generate(Resource $resource)
    {
        return Parser::encode($this->toArray($resource), JSON_PRETTY_PRINT);
    }

    public function toArray(Resource $resource)
    {
        $definitions = array();
        $properties  = array();

        // path
        if ($resource->hasPathParameters()) {
            list($defs, $refs) = $this->getJsonSchema($resource->getPathParameters());

            $definitions = array_merge($definitions, $defs);

            if (!empty($refs)) {
                $key = 'path';

                $properties[$key] = $refs;
            }
        }

        // methods
        $methods = $resource->getMethods();
        foreach ($methods as $method) {
            // query
            if ($method->hasQueryParameters()) {
                list($defs, $refs) = $this->getJsonSchema($method->getQueryParameters());

                $definitions = array_merge($definitions, $defs);

                if (!empty($refs)) {
                    $key = $method->getName() . '-query';

                    $properties[$key] = $refs;
                }
            }

            // request
            if ($method->hasRequest()) {
                list($defs, $refs) = $this->getJsonSchema($method->getRequest());

                $definitions = array_merge($definitions, $defs);

                if (!empty($refs)) {
                    $key = $method->getName() . '-request';

                    $properties[$key] = $refs;
                }
            }

            // response
            $responses = $method->getResponses();
            foreach ($responses as $statusCode => $response) {
                list($defs, $refs) = $this->getJsonSchema($response);

                $definitions = array_merge($definitions, $defs);

                if (!empty($refs)) {
                    $key = $method->getName() . '-' . $statusCode . '-response';

                    $properties[$key] = $refs;
                }
            }
        }

        $definitions = array_merge($definitions, $properties);

        return array(
            '$schema'     => JsonSchemaGenerator::SCHEMA,
            'id'          => $this->targetNamespace,
            'type'        => 'object',
            'definitions' => $definitions,
        );
    }

    protected function getJsonSchema(SchemaInterface $schema)
    {
        $generator   = new JsonSchemaGenerator($this->targetNamespace);
        $data        = $generator->toArray($schema);
        $definitions = array();

        unset($data['$schema']);
        unset($data['id']);

        if (isset($data['definitions'])) {
            $definitions = $data['definitions'];

            unset($data['definitions']);
        }

        $type  = isset($data['type']) ? $data['type'] : null;
        $props = null;

        if (isset($data['properties'])) {
            $key = 'ref' . $schema->getDefinition()->getId();
            if (!isset($definitions[$key])) {
                $definitions[$key] = $data;
            }

            $props = ['$ref' => '#/definitions/' . $key];
        }

        return [$definitions, $props];
    }
}
