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

namespace PSX\Schema\Parser\JsonSchema\Resolver;

use PSX\Schema\Parser\JsonSchema\Document;
use PSX\Schema\Parser\JsonSchema\RefResolver;
use PSX\Schema\Parser\JsonSchema\ResolverInterface;
use PSX\Http\Client as HttpClient;
use PSX\Http\GetRequest;
use PSX\Json\Parser;
use PSX\Uri\Uri;
use PSX\Uri\UriResolver;
use RuntimeException;

/**
 * Http
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Http implements ResolverInterface
{
    protected $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function resolve(Uri $uri, Document $source, RefResolver $resolver)
    {
        if (!$uri->isAbsolute()) {
            $uri = UriResolver::resolve($source->getBaseUri(), $uri);
        }

        $request  = new GetRequest($uri, array('Accept' => 'application/schema+json'));
        $response = $this->httpClient->request($request);

        if ($response->getStatusCode() == 200) {
            $schema   = (string) $response->getBody();
            $data     = Parser::decode($schema, true);
            $document = new Document($data, $resolver, null, $uri);

            return $document;
        } else {
            throw new RuntimeException('Could not load external schema ' . $uri->toString() . ' received ' . $response->getStatusCode());
        }
    }
}
