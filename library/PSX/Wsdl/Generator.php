<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Wsdl;

use DOMDocument;
use InvalidArgumentException;
use PSX\Config;
use PSX\Api\View;
use PSX\Data\Schema\Generator\Xsd as XsdGenerator;

/**
 * WsdlGenerator
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Generator
{
	const VERSION_1 = 0x1;
	const VERSION_2 = 0x2;

	protected $endpoint;
	protected $version;
	protected $targetNamespace;
	protected $schemaNamespace;

	protected $types = array();

	public function __construct($version, $endpoint, $targetNamespace, $schemaNamespace)
	{
		$this->version         = $version;
		$this->endpoint        = $endpoint;
		$this->targetNamespace = $targetNamespace;
		$this->schemaNamespace = $schemaNamespace;
	}

	public function generate(View $views)
	{
		$this->types = array();

		$generator  = $this->getGenerator();
		$types      = $this->getTypes($views);
		$operations = $this->getOperations($views);
		
		return $generator->generate($this->targetNamespace, $this->schemaNamespace, $types, $operations);
	}

	protected function getTypes(View $views)
	{
		$xsdGenerator = new XsdGenerator($this->schemaNamespace);
		$types        = array();

		foreach($views as $view)
		{
			$dom = new DOMDocument();
			$dom->loadXML($xsdGenerator->generate($view));

			foreach($dom->documentElement->childNodes as $childNode)
			{
				if($childNode->nodeType == XML_ELEMENT_NODE)
				{
					$name = $childNode->getAttribute('name');

					if(!empty($name) && !in_array($name, $this->types))
					{
						$types[] = $childNode;

						$this->types[] = $name;
					}
				}
			}
		}

		return $types;
	}

	protected function getOperations(View $views)
	{
		$operations = array();
		$methods    = array(View::METHOD_GET, View::METHOD_POST, View::METHOD_PUT, View::METHOD_DELETE);

		foreach($methods as $method)
		{
			if($views->has($method))
			{
				if($views->has($method | View::TYPE_REQUEST))
				{
					$entityName = $views->get($method | View::TYPE_REQUEST)->getDefinition()->getName();
				}
				else if($views->has($method | View::TYPE_RESPONSE))
				{
					$entityName = $views->get($method | View::TYPE_RESPONSE)->getDefinition()->getName();
				}

				$methodName = $this->getMethodNameByModifier($method);
				$endpoint   = $this->endpoint . '?_method=' . $methodName;

				$operation = new Operation($methodName . ucfirst($entityName));
				$operation->setEndpoint($endpoint);

				if($views->has($method | View::TYPE_REQUEST))
				{
					$operation->setIn($views->get($method | View::TYPE_REQUEST)->getDefinition()->getName());
				}

				if($views->has($method | View::TYPE_RESPONSE))
				{
					$operation->setOut($views->get($method | View::TYPE_RESPONSE)->getDefinition()->getName());
				}

				if($operation->hasOperation())
				{
					$operations[] = $operation;
				}
			}
		}

		return $operations;
	}

	protected function getMethodNameByModifier($modifier)
	{
		if($modifier & View::METHOD_GET)
		{
			return 'get';
		}
		else if($modifier & View::METHOD_POST)
		{
			return 'post';
		}
		else if($modifier & View::METHOD_PUT)
		{
			return 'put';
		}
		else if($modifier & View::METHOD_DELETE)
		{
			return 'delete';
		}
	}

	protected function getTypeNameByModifier($modifier)
	{
		if($modifier & View::TYPE_REQUEST)
		{
			return 'request';
		}
		else if($modifier & View::TYPE_RESPONSE)
		{
			return 'response';
		}
	}

	protected function getGenerator()
	{
		if($this->version == self::VERSION_1)
		{
			return new Generator\Version1();
		}
		else if($this->version == self::VERSION_2)
		{
			return new Generator\Version2();
		}
		else
		{
			throw new InvalidArgumentException('Unknown version');
		}
	}
}
