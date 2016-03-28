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

namespace PSX\Api\Resource\Generator;

use PSX\Api\Resource;
use PSX\Api\Resource\GeneratorInterface;
use PSX\Http\Http;
use PSX\Schema\SchemaInterface;

/**
 * HtmlAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class HtmlAbstract implements GeneratorInterface
{
    const TYPE_PATH     = 0x1;
    const TYPE_QUERY    = 0x2;
    const TYPE_REQUEST  = 0x3;
    const TYPE_RESPONSE = 0x4;

    public function generate(Resource $resource)
    {
        $class = strtolower(str_replace('\\', '-', get_class($this)));

        $html = '<div class="psx-resource ' . $class . '" data-status="' . $resource->getStatus() . '" data-path="' . $resource->getPath() . '">';
        $html.= '<h4>' . $this->getName() . '</h4>';

        $description = $resource->getDescription();
        if (!empty($description)) {
            $html.= '<div class="psx-resource-description">' . $description . '</div>';
        }

        $methods = $resource->getMethods();
        foreach ($methods as $method) {
            $html.= '<div class="psx-resource-method" data-method="' . $method->getName() . '">';

            $description = $method->getDescription();
            if (!empty($description)) {
                $html.= '<div class="psx-resource-method-description">' . $description . '</div>';
            }

            // path parameters
            $pathParameters = $resource->getPathParameters();

            if ($pathParameters instanceof SchemaInterface) {
                $result = $this->generateHtml($pathParameters, self::TYPE_PATH, $method->getName(), $resource->getPath());

                if (!empty($result)) {
                    $html.= '<div class="psx-resource-data psx-resource-query">';
                    $html.= '<h5>' . $method->getName() . ' Path-Parameters</h5>';
                    $html.= '<div class="psx-resource-data-content">' . $result . '</div>';
                    $html.= '</div>';
                }
            }

            // query parameters
            $queryParameters = $method->getQueryParameters();

            if ($queryParameters instanceof SchemaInterface) {
                $result = $this->generateHtml($queryParameters, self::TYPE_QUERY, $method->getName(), $resource->getPath());

                if (!empty($result)) {
                    $html.= '<div class="psx-resource-data psx-resource-query">';
                    $html.= '<h5>' . $method->getName() . ' Query-Parameters</h5>';
                    $html.= '<div class="psx-resource-data-content">' . $result . '</div>';
                    $html.= '</div>';
                }
            }

            // request
            $request = $method->getRequest();

            if ($request instanceof SchemaInterface) {
                $result = $this->generateHtml($request, self::TYPE_REQUEST, $method->getName(), $resource->getPath());

                if (!empty($result)) {
                    $html.= '<div class="psx-resource-data psx-resource-request">';
                    $html.= '<h5>' . $method->getName() . ' Request</h5>';
                    $html.= '<div class="psx-resource-data-content">' . $result . '</div>';
                    $html.= '</div>';
                }
            }

            // responses
            $responses = $method->getResponses();

            foreach ($responses as $statusCode => $response) {
                $result = $this->generateHtml($response, self::TYPE_RESPONSE, $method->getName(), $resource->getPath(), $statusCode);

                if (!empty($result)) {
                    $message = isset(Http::$codes[$statusCode]) ? Http::$codes[$statusCode] : 'Unknown';

                    $html.= '<div class="psx-resource-data psx-resource-response">';
                    $html.= '<h5>' . $method->getName() . ' Response - ' . $statusCode . ' ' . $message . '</h5>';
                    $html.= '<div class="psx-resource-data-content">' . $result . '</div>';
                    $html.= '</div>';
                }
            }

            $html.= '</div>';
        }

        $html.= '</div>';

        return $html;
    }

    /**
     * Returns the name of the html generator
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Returns an html chunk for the specific schema
     *
     * @param \PSX\Schema\SchemaInterface $schema
     * @param integer $type
     * @param string $method
     * @param string $path
     * @param string $statusCode
     * @return string
     */
    abstract protected function generateHtml(SchemaInterface $schema, $type, $method, $path, $statusCode = null);
}
