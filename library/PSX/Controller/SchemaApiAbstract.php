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

namespace PSX\Controller;

use PSX\Api\DocumentationInterface;
use PSX\Api\DocumentedInterface;
use PSX\Api\Resource;
use PSX\Api\Resource\MethodAbstract;
use PSX\Api\Version;
use PSX\Data\Record;
use PSX\Data\RecordInterface;
use PSX\Data\SchemaInterface;
use PSX\Http\Exception as StatusCode;
use PSX\Loader\Context;
use RuntimeException;

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
     * @var \PSX\Data\Schema\Assimilator
     */
    protected $schemaAssimilator;

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

    /**
     * @var \PSX\Api\Version
     */
    protected $version;

    public function onLoad()
    {
        parent::onLoad();

        $doc = $this->resourceListing->getDocumentation($this->context->get(Context::KEY_PATH));

        if (!$doc instanceof DocumentationInterface) {
            throw new StatusCode\InternalServerErrorException('No documentation available for this resource');
        }

        $this->version  = $this->getVersion($doc);
        $this->resource = $this->getResource($doc, $this->version);

        if (!$this->resource->hasMethod($this->getMethod())) {
            throw new StatusCode\MethodNotAllowedException('Method is not allowed', $this->resource->getAllowedMethods());
        }

        $this->pathParameters  = $this->schemaAssimilator->assimilate(
            $this->resource->getPathParameters(),
            $this->uriFragments
        );

        $this->queryParameters = $this->schemaAssimilator->assimilate(
            $this->resource->getMethod($this->getMethod())->getQueryParameters(),
            $this->request->getUri()->getParameters()
        );
    }

    public function onHead()
    {
        $method   = $this->resource->getMethod('GET');
        $response = $this->doGet($this->version);

        // the setResponse method removes the body so we behave like on a GET 
        // request
        $this->sendResponse($method, $response);
    }

    public function onGet()
    {
        $method   = $this->resource->getMethod('GET');
        $response = $this->doGet($this->version);

        $this->sendResponse($method, $response);
    }

    public function onPost()
    {
        $method = $this->resource->getMethod('POST');
        $record = $this->parseRequest($method);

        // compatibility layer to transition from doCreate to doPost. The 
        // doCreate call will be removed in the next major version
        if (method_exists($this, 'doCreate')) {
            $response = $this->doCreate($record, $this->version);
        } else {
            $response = $this->doPost($record, $this->version);
        }

        $this->sendResponse($method, $response);
    }

    public function onPut()
    {
        $method = $this->resource->getMethod('PUT');
        $record = $this->parseRequest($method);

        // compatibility layer to transition from doUpdate to doPut. The 
        // doUpdate call will be removed in the next major version
        if (method_exists($this, 'doUpdate')) {
            $response = $this->doUpdate($record, $this->version);
        } else {
            $response = $this->doPut($record, $this->version);
        }

        $this->sendResponse($method, $response);
    }

    public function onDelete()
    {
        $method   = $this->resource->getMethod('DELETE');
        $record   = $this->parseRequest($method);
        $response = $this->doDelete($record, $this->version);

        $this->sendResponse($method, $response);
    }

    public function onPatch()
    {
        $method   = $this->resource->getMethod('PATCH');
        $record   = $this->parseRequest($method);
        $response = $this->doPatch($record, $this->version);

        $this->sendResponse($method, $response);
    }

    /**
     * Handles a GET request and returns a response
     *
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.1
     * @param \PSX\Api\Version $version
     * @return mixed
     */
    protected function doGet(Version $version)
    {
    }

    /**
     * Handles a POST request and returns a response
     *
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.3
     * @param \PSX\Data\RecordInterface $record
     * @param \PSX\Api\Version $version
     * @return mixed
     */
    protected function doPost(RecordInterface $record, Version $version)
    {
    }

    /**
     * Handles a PUT request and returns a response
     *
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.4
     * @param \PSX\Data\RecordInterface $record
     * @param \PSX\Api\Version $version
     * @return mixed
     */
    protected function doPut(RecordInterface $record, Version $version)
    {
    }

    /**
     * Handles a DELETE request and returns a response
     *
     * @see http://tools.ietf.org/html/rfc7231#section-4.3.5
     * @param \PSX\Data\RecordInterface $record
     * @param \PSX\Api\Version $version
     * @return mixed
     */
    protected function doDelete(RecordInterface $record, Version $version)
    {
    }

    /**
     * Handles a PATCH request and returns a response
     *
     * @see https://tools.ietf.org/html/rfc5789#section-2
     * @param \PSX\Data\RecordInterface $record
     * @param \PSX\Api\Version $version
     * @return mixed
     */
    protected function doPatch(RecordInterface $record, Version $version)
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
        return $method->hasRequest() ? $this->import($method->getRequest()) : new Record();
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
            $this->setBody($this->schemaAssimilator->assimilate($schema, $response));
        } else {
            $this->setResponseCode(204);
            $this->setBody('');
        }
    }

    /**
     * Returns the resource from the documentation for the given version.
     *
     * @param \PSX\Api\DocumentationInterface $doc
     * @param \PSX\Api\Version $version
     * @return \PSX\Api\Resource
     */
    protected function getResource(DocumentationInterface $doc, Version $version)
    {
        if (!$doc->hasResource($version->getVersion())) {
            throw new StatusCode\NotAcceptableException('Version is not available');
        }

        $resource = $doc->getResource($version->getVersion());

        if ($resource->isActive()) {
        } elseif ($resource->isDeprecated()) {
            $this->response->addHeader('Warning', '199 PSX "Version v' . $version->getVersion() . ' is deprecated"');
        } elseif ($resource->isClosed()) {
            throw new StatusCode\GoneException('Version v' . $version->getVersion() . ' is not longer supported');
        } elseif ($resource->isDevelopment()) {
            $this->response->addHeader('Warning', '199 PSX "Version v' . $version->getVersion() . ' is in development"');
        }

        return $resource;
    }

    /**
     * Returns the version which was provided by the user agent. If no version
     * was specified the latest version is used
     *
     * @param \PSX\Api\DocumentationInterface $doc
     * @return \PSX\Api\Version
     */
    protected function getVersion(DocumentationInterface $doc)
    {
        if ($doc->isVersionRequired()) {
            $version = $this->getSubmittedVersionNumber();

            if ($version !== null) {
                return new Version((int) $version);
            } else {
                // it is strongly recommended that clients specify an explicit
                // version but forcing that with an exception is not a good user
                // experience therefore we use the latest version if nothing is
                // specified
                return new Version($doc->getLatestVersion());

                //throw new StatusCode\UnsupportedMediaTypeException('Requires an Accept header containing an explicit version');
            }
        } else {
            return new Version(1);
        }
    }

    /**
     * Returns the successful response of an method or null if no is available
     *
     * @param \PSX\Api\Resource\MethodAbstract $method
     * @param integer $statusCode
     * @return \PSX\Data\SchemaInterface
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

    /**
     * Returns the version number which was submitted by the client in the
     * accept header field
     *
     * @return integer
     */
    protected function getSubmittedVersionNumber()
    {
        $accept  = $this->getHeader('Accept');
        $matches = array();

        preg_match('/^application\/vnd\.([a-z.-_]+)\.v([\d]+)\+([a-z]+)$/', $accept, $matches);

        return isset($matches[2]) ? $matches[2] : null;
    }
}
