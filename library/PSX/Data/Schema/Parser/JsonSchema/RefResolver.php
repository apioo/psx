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

	public function setRootDocument(Document $document)
	{
		$this->documents = [$document];
	}

	/**
	 * Resolves an $ref to an property
	 *
	 * @param PSX\Data\Schema\Parser\JsonSchema\Document $document
	 * @param PSX\Uri $ref
	 * @param string $name
	 * @param integer $depth
	 * @return PSX\Data\Schema\PropertyInterface
	 */
	public function resolve(Document $document, Uri $ref, $name, $depth)
	{
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

		$uri = $this->resolver->resolve($document->getBaseUri(), $ref);
		$doc = $this->getDocument($uri, $document);

		$this->recursionPath[$depth] = $uri->toString();

		return $doc->getProperty($uri->getFragment(), $name, $depth);
	}

	/**
	 * Extracts an array part from the document
	 *
	 * @param PSX\Data\Schema\Parser\JsonSchema\Document $document
	 * @param PSX\Uri $ref
	 * @return array
	 */
	public function extract(Document $document, Uri $ref)
	{
		$uri     = $this->resolver->resolve($document->getBaseUri(), $ref);
		$doc     = $this->getDocument($uri, $document);
		$result  = $doc->pointer($uri->getFragment());
		$baseUri = $doc->getBaseUri();

		// the extracted fragment gets merged into the root document so we must
		// resolve all $ref keys to the base uri so that the root document knows
		// where to find the $ref values
		array_walk_recursive($result, function(&$item, $key) use ($baseUri){

			if($key == '$ref')
			{
				$item = $this->resolver->resolve($baseUri, new Uri($item))->toString();
			}

		});

		return $result;
	}

	protected function getDocument(Uri $uri, Document $sourceDocument)
	{
		// check whether we have already a document assigned to the base path
		$document = $this->getDocumentById($uri);

		if($document instanceof Document && $document->canResolve($uri))
		{
			return $document;
		}

		// load the remote document
		if($uri->getScheme() == 'file' && !$sourceDocument->isRemote())
		{
			$path = str_replace('/', DIRECTORY_SEPARATOR, ltrim($uri->getPath(), '/'));
			$path = $sourceDocument->getBasePath() !== null ? $sourceDocument->getBasePath() . DIRECTORY_SEPARATOR . $path : $path;

			if(is_file($path))
			{
				$basePath = pathinfo($path, PATHINFO_DIRNAME);
				$schema   = file_get_contents($path);
				$data     = Json::decode($schema);
				$document = new Document($data, $this, $basePath, $uri);

				$this->documents[] = $document;

				return $document;
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
				$document = new Document($data, $this, null, $uri);

				$this->documents[] = $document;

				return $document;
			}
			else
			{
				throw new \RuntimeException('Could not load external schema ' . $uri->toString() . ' received ' . $response->getStatusCode());
			}
		}
		else
		{
			throw new \RuntimeException('Unknown protocol for external resource ' . $uri->getScheme());
		}
	}

	protected function getDocumentById(Uri $uri)
	{
		$key = $this->getKey($uri);

		foreach($this->documents as $document)
		{
			if($key == $this->getKey($document->getBaseUri()))
			{
				return $document;
			}
			else if($document->getSource() != null && $key == $this->getKey($document->getSource()))
			{
				return $document;
			}
		}

		return null;
	}

	protected function getKey(Uri $uri)
	{
		return $uri->getScheme() . '-' . $uri->getHost() . '-' . $uri->getPath();
	}
}
