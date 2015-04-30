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

namespace PSX\Data\Schema\Parser;

use PSX\Data\Schema;
use PSX\Data\Schema\Property;
use PSX\Data\Schema\PropertyInterface;
use PSX\Data\Schema\PropertySimpleAbstract;
use PSX\Data\Schema\ParserInterface;
use PSX\Data\Schema\Parser\JsonSchema\Document;
use PSX\Data\Schema\Parser\JsonSchema\RefResolver;
use PSX\Data\Schema\Parser\JsonSchema\UnsupportedVersionException;
use PSX\Json;
use PSX\Uri;
use PSX\Util\UriResolver;
use RuntimeException;

/**
 * JsonSchema
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class JsonSchema implements ParserInterface
{
	const SCHEMA_04 = 'http://json-schema.org/draft-04/schema#';

	protected $basePath;
	protected $resolver;

	public function __construct($basePath = null, RefResolver $resolver = null)
	{
		$this->basePath = $basePath;
		$this->resolver = $resolver ?: new RefResolver();
	}

	public function parse($schema)
	{
		$data     = Json::decode($schema);
		$document = new Document($data, $this->resolver, $this->basePath);

		$this->resolver->setRootDocument($document);

		return new Schema($document->getProperty());
	}

	public static function fromFile($file)
	{
		if(!empty($file) && is_file($file))
		{
			$basePath = pathinfo($file, PATHINFO_DIRNAME);
			$parser   = new self($basePath);

			return $parser->parse(file_get_contents($file));
		}
		else
		{
			throw new RuntimeException('Could not load json schema ' . $file);
		}
	}
}
