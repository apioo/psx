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

namespace PSX\Framework\Controller;

use PSX\Api\DocumentedInterface;
use PSX\Api\Resource;
use PSX\Api\Resource\MethodAbstract;
use PSX\Framework\Loader\Context;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Http\Exception as StatusCode;
use PSX\Schema\SchemaInterface;

/**
 * SchemaApiAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link	http://phpsx.org
 */
abstract class SchemaApiAbstract extends ApiAbstract implements DocumentedInterface
{
    /**
     * @Inject
     * @var \PSX\Api\Resource\ListingInterface
     */
    protected $resourceListing;

    /**
     * @var \PSX\Data\Record
     */
    protected $queryParameters;

    /**
     * @var \PSX\Data\Record
     */
    protected $pathParameters;

    /**
     * @var \PSX\Api\Resource
     */
    protected $resource;

    public function onLoad()
    {
        parent::onLoad();

        // get the current resource and check everything is valid
        $this->resource = $this->getResource();

        // validate and assign the path parameters
        $this->pathParameters = $this->io->assimilate(
            $this->uriFragments,
            $this->resource->getPathParameters()
        );

        // validate and assign the query parameters
        $this->queryParameters = $this->io->assimilate(
            $this->request->getUri()->getParameters(),
            $this->resource->getMethod($this->getMethod())->getQueryParameters()
        );
    }

    public function onHead()
    {
        $method   = $this->resource->getMethod('GET');
        $response = $this->doGet();

        // the setResponse method removes the body so we behave like on a GET 
        // request
        $this->sendResponse($method, $response);
    }

    public function onGet()
    {
        $method   = $this->resource->getMethod('GET');
        $response = $this->doGet();

        $this->sendResponse($method, $response);
    }

    public function onPost()
    {
        $method = $this->resource->getMethod('POST');
        $record = $this->parseRequest($method);

        $response = $this->doPost($record);

        $this->sendResponse($method, $response);
    }

    public function onPut()
    {
        $method = $this->resource->getMethod('PUT');
        $record = $this->parseRequest($method);

        $response = $this->doPut($record);

        $this->sendResponse($method, $response);
    }

    public function onDelete()
    {
        $method   = $this->resource->getMethod('DELETE');
        $record   = $this->parseRequest($method);
        $response = $this->doDelete($record);

        $this->sendResponse($method, $response);
    }

    public function onPatch()
    {
        $method   = $this->resource->getMethod('PATCH');
        $record   = $this->parseRequest($method);
        $response = $this->doPatch($record);

        $this->sendResponse($method, $response);
    }

    /**
     * Handles a GET request and returns a response
     *
     * @Exclude
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.1
     * @return mixed
     */
    protected function doGet()
    {
    }

    /**
     * Handles a POST request and returns a response
     *
     * @Exclude
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.3
     * @param \PSX\Data\RecordInterface $record
     * @return mixed
     */
    protected function doPost(RecordInterface $record)
    {
    }

    /**
     * Handles a PUT request and returns a response
     *
     * @Exclude
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.4
     * @param \PSX\Data\RecordInterface $record
     * @return mixed
     */
    protected function doPut(RecordInterface $record)
    {
    }

    /**
     * Handles a DELETE request and returns a response
     *
     * @Exclude
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.5
     * @param \PSX\Data\RecordInterface $record
     * @return mixed
     */
    protected function doDelete(RecordInterface $record)
    {
    }

    /**
     * Handles a PATCH request and returns a response
     *
     * @Exclude
     * @see https://tools.ietf.org/html/rfc5789#section-2
     * @param \PSX\Data\RecordInterface $record
     * @return mixed
     */
    protected function doPatch(RecordInterface $record)
    {
    }

    /**
     * Imports the request data based on the schema if available
     *
     * @param \PSX\Api\Resource\MethodAbstract $method
     * @return \PSX\Data\RecordInterface
     */
    protected function parseRequest(MethodAbstract $method)
    {
        return $method->hasRequest() ? $this->getBodyAs($method->getRequest(), $this->getValidator($method)) : new Record();
    }

    /**
     * Returns the validator which is used for a specific request method
     *
     * @param \PSX\Api\Resource\MethodAbstract $method
     * @return \PSX\Validate\Validator
     */
    protected function getValidator(MethodAbstract $method)
    {
        return null;
    }

    /**
     * Gets the schema for the status code and formats the response according to
     * the schema. If no status code was provided the schema of an successful
     * response is taken
     *
     * @param \PSX\Api\Resource\MethodAbstract $method
     * @param mixed $response
     */
    protected function sendResponse(MethodAbstract $method, $response)
    {
        $statusCode = $this->response->getStatusCode();
        if (!empty($statusCode) && $method->hasResponse($statusCode)) {
            $schema = $method->getResponse($statusCode);
        } else {
            $schema = $this->getSuccessfulResponse($method, $statusCode);
        }

        if ($schema instanceof SchemaInterface) {
            $this->setResponseCode($statusCode);
            $this->setBodyAs($response, $schema);
        } else {
            $this->setResponseCode(204);
            $this->setBody('');
        }
    }

    /**
     * Returns the resource from the listing for the current path
     *
     * @return \PSX\Api\Resource
     */
    protected function getResource()
    {
        $resource = $this->resourceListing->getResource($this->context->get(Context::KEY_PATH));

        if (!$resource instanceof Resource) {
            throw new StatusCode\InternalServerErrorException('Resource is not available');
        }

        if ($resource->isActive()) {
        } elseif ($resource->isDeprecated()) {
            $this->response->addHeader('Warning', '199 PSX "Resource is deprecated"');
        } elseif ($resource->isClosed()) {
            throw new StatusCode\GoneException('Resource is not longer supported');
        } elseif ($resource->isDevelopment()) {
            $this->response->addHeader('Warning', '199 PSX "Resource is in development"');
        }

        if (!$resource->hasMethod($this->getMethod())) {
            throw new StatusCode\MethodNotAllowedException('Method is not allowed', $resource->getAllowedMethods());
        }

        return $resource;
    }

    /**
     * Returns the successful response of a method or null if no is available
     *
     * @param \PSX\Api\Resource\MethodAbstract $method
     * @param integer $statusCode
     * @return \PSX\Schema\SchemaInterface
     */
    protected function getSuccessfulResponse(MethodAbstract $method, &$statusCode)
    {
        $successCodes = [200, 201, 202, 203, 205, 207];

        foreach ($successCodes as $successCode) {
            if ($method->hasResponse($successCode)) {
                $statusCode = $successCode;

                return $method->getResponse($successCode);
            }
        }

        return null;
    }
}
