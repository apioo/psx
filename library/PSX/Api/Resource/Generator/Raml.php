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

namespace PSX\Api\Resource\Generator;

use PSX\Api\Resource;
use PSX\Api\Resource\GeneratorAbstract;
use PSX\Data\Schema\Generator as SchemaGenerator;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;
use PSX\Util\ApiGeneration;
use Symfony\Component\Yaml\Inline;

/**
 * Raml
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Raml extends GeneratorAbstract
{
    protected $title;
    protected $version;
    protected $baseUri;
    protected $targetNamespace;

    public function __construct($title, $version, $baseUri, $targetNamespace)
    {
        $this->title           = $title;
        $this->version         = $version;
        $this->baseUri         = $baseUri;
        $this->targetNamespace = $targetNamespace;
    }

    public function generate(Resource $resource)
    {
        $path        = ApiGeneration::transformRoutePlaceholder($resource->getPath() ?: '/');
        $description = $resource->getDescription();

        $raml = '#%RAML 0.8' . "\n";
        $raml.= '---' . "\n";
        $raml.= 'baseUri: ' . Inline::dump($this->baseUri) . "\n";
        $raml.= 'version: v' . $this->version . "\n";
        $raml.= 'title: ' . Inline::dump($this->title) . "\n";
        $raml.= $path . ':' . "\n";

        if (!empty($description)) {
            $raml.= '  description: ' . Inline::dump($description) . "\n";
        }

        // path parameter
        $parameters = $resource->getPathParameters()->getDefinition();

        if (count($parameters) > 0) {
            $raml.= '  uriParameters:' . "\n";

            foreach ($parameters as $parameter) {
                $raml.= '    ' . $parameter->getName() . ':' . "\n";

                $this->setParameterType($parameter, $raml, 6);
            }
        }

        $generator = new SchemaGenerator\JsonSchema($this->targetNamespace);
        $methods   = $resource->getMethods();

        foreach ($methods as $method) {
            $raml.= '  ' . strtolower($method->getName()) . ':' . "\n";

            // description
            $description = $method->getDescription();
            if (!empty($description)) {
                $raml.= '    description: ' . Inline::dump($description) . "\n";
            }

            // query parameter
            $parameters = $method->getQueryParameters()->getDefinition();

            if (count($parameters) > 0) {
                $raml.= '    queryParameters:' . "\n";

                foreach ($parameters as $parameter) {
                    $raml.= '      ' . $parameter->getName() . ':' . "\n";

                    $this->setParameterType($parameter, $raml, 8);
                }
            }

            // request body
            if ($method->hasRequest()) {
                $schema = $generator->generate($method->getRequest());
                $schema = str_replace("\n", "\n          ", $schema);

                $raml.= '    body:' . "\n";
                $raml.= '      application/json:' . "\n";
                $raml.= '        schema: |' . "\n";
                $raml.= '          ' . $schema . "\n";
            }

            // response body
            $raml.= '    responses:' . "\n";

            $responses = $method->getResponses();

            foreach ($responses as $statusCode => $response) {
                $schema = $generator->generate($response);
                $schema = str_replace("\n", "\n              ", $schema);

                $raml.= '      ' . $statusCode . ':' . "\n";
                $raml.= '        body:' . "\n";
                $raml.= '          application/json:' . "\n";
                $raml.= '            schema: |' . "\n";
                $raml.= '              ' . $schema . "\n";
            }
        }

        return $raml;
    }

    protected function setParameterType(PropertyInterface $parameter, &$raml, $indent)
    {
        $indent = str_repeat(' ', $indent);

        switch (true) {
            case $parameter instanceof Property\IntegerType:
                $raml.= $indent . 'type: integer' . "\n";
                break;

            case $parameter instanceof Property\FloatType:
                $raml.= $indent . 'type: number' . "\n";
                break;

            case $parameter instanceof Property\BooleanType:
                $raml.= $indent . 'type: boolean' . "\n";
                break;

            case $parameter instanceof Property\DateType:
            case $parameter instanceof Property\DateTimeType:
                $raml.= $indent . 'type: date' . "\n";
                break;

            default:
                $raml.= $indent . 'type: string' . "\n";
                break;
        }

        $description = $parameter->getDescription();

        if (!empty($description)) {
            $raml.= $indent . 'description: ' . Inline::dump($parameter->getDescription()) . "\n";
        }

        $raml.= $indent . 'required: ' . ($parameter->isRequired() ? 'true' : 'false') . "\n";

        if ($parameter instanceof Property\DecimalType) {
            $min = $parameter->getMin();
            $max = $parameter->getMax();

            if ($min !== null) {
                $raml.= $indent . 'minimum: ' . $min . "\n";
            }

            if ($max !== null) {
                $raml.= $indent . 'maximum: ' . $max . "\n";
            }
        } elseif ($parameter instanceof Property\StringType) {
            $minLength   = $parameter->getMinLength();
            $maxLength   = $parameter->getMaxLength();
            $enumeration = $parameter->getEnumeration();
            $pattern     = $parameter->getPattern();

            if ($minLength !== null) {
                $raml.= $indent . 'minLength: ' . $minLength . "\n";
            }

            if ($maxLength !== null) {
                $raml.= $indent . 'maxLength: ' . $maxLength . "\n";
            }

            if (!empty($enumeration)) {
                $raml.= $indent . 'enum: ' . Inline::dump($enumeration) . "\n";
            }

            if (!empty($pattern)) {
                $raml.= $indent . 'pattern: ' . Inline::dump($pattern) . "\n";
            }
        }
    }
}
