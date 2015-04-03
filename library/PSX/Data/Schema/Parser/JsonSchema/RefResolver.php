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

namespace PSX\Data\Schema\Parser\JsonSchema;

use PSX\Http;
use PSX\Http\GetRequest;
use PSX\Json;
use PSX\Uri;
use PSX\Util\UriResolver;

/**
 * RefResolver
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RefResolver
{
	protected $resolver;
	protected $http;
	protected $documents = array();

	protected $recursionPath = array();

	public function __construct(Http $http = null, UriResolver $uriResolver = null)
	{
		$this->http     = $http ?: new Http();
		$this->resolver = $uriResolver ?: new UriResolver();
	}

	public function resolve(Document $document, Uri $ref, $name, $depth)
	{
		$uri = $this->resolver->resolve($document->getBaseUri(), $ref);

		// recursion detection
		$count = count($this->recursionPath);

		if($count > 0 && $count % 2 == 0)
		{
			$len = $count / 2;

			if(array_slice($this->recursionPath, 0, $len) === array_slice($this->recursionPath, $len))
			{
				return null;
			}
		}

		if($depth < $count)
		{
			$this->recursionPath = array_slice($this->recursionPath, 0, $depth);
		}

		$this->recursionPath[$depth] = $uri->toString();

		if($document->canResolve($uri))
		{
			// we resolve the $ref on the current document
			return $document->pointer($uri->getFragment(), $name, $depth);
		}
		else
		{
			// check whether we have already fetched a document which can
			// resolve the $ref
			foreach($this->documents as $document)
			{
				if($document->canResolve($uri))
				{
					return $document->pointer($uri->getFragment(), $name, $depth);
				}
			}

			// load the remote document
			if($uri->getScheme() == 'file' && !$document->isRemote())
			{
				$path = str_replace('/', DIRECTORY_SEPARATOR, ltrim($uri->getPath(), '/'));
				$path = $document->getBasePath() !== null ? $document->getBasePath() . DIRECTORY_SEPARATOR . $path : $path;

				if(!empty($path) && is_file($path))
				{
					$basePath = pathinfo($path, PATHINFO_DIRNAME);
					$schema   = file_get_contents($path);
					$data     = Json::decode($schema);
					$document = new Document($data, $this, $basePath);

					$this->documents[] = $document;

					return $document->pointer($uri->getFragment(), $name, $depth);
				}
				else
				{
					throw new \RuntimeException('Could not load external schema ' . $path);
				}
			}
			else if(in_array($uri->getScheme(), ['http', 'https']))
			{
				$request  = new GetRequest($uri, array('Accept' => 'application/schema+json'));
				$response = $this->http->request($request);

				if($response->getStatusCode() == 200)
				{
					$schema   = (string) $response->getBody();
					$data     = Json::decode($schema);
					$document = new Document($data, $this);

					$this->documents[] = $document;

					return $document->pointer($uri->getFragment(), $name, $depth);
				}
				else
				{
					throw new \RuntimeException('Could not load external schema ' . $uri->toString() . ' received ' . $response->getStatusCode());
				}
			}
			else
			{
				throw new \RuntimeException('Unknown protocol ' . $uri->getScheme() . ' for external resource');
			}
		}

		return array();
	}
}
