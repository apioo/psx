<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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
use DOMElement;
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

	protected $version;
	protected $name;
	protected $endpoint;
	protected $targetNamespace;

	protected $types = array();

	public function __construct($version, $name, $endpoint, $targetNamespace)
	{
		$this->version         = $version;
		$this->name            = $name;
		$this->endpoint        = $endpoint;
		$this->targetNamespace = $targetNamespace;
	}

	public function generate(View $views)
	{
		$this->types = array();

		$generator  = $this->getGenerator();
		$types      = $this->getTypes($views);
		$operations = $this->getOperations($views);
		
		return $generator->generate($this->name, $this->endpoint, $this->targetNamespace, $types, $operations);
	}

	protected function getTypes(View $views)
	{
		$xsdGenerator = new XsdGenerator($this->targetNamespace);
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

					if(!empty($name))
					{
						if($childNode->nodeName == 'xs:element')
						{
							// if we have an element check whether it contains an complex type
							$complexTypes = $this->getElementsByTagName($childNode, 'xs:complexType');

							foreach($complexTypes as $complexType)
							{
								$complexType->setAttribute('name', $name);

								$types[] = $complexType;
							}
						}
						else
						{
							$types[] = $childNode;
						}
					}
				}
			}
		}

		// add default schema types
		$dom = new DOMDocument();
		$dom->loadXML($this->getDefaultSchema());

		foreach($dom->documentElement->childNodes as $childNode)
		{
			if($childNode->nodeType == XML_ELEMENT_NODE)
			{
				$name = $childNode->getAttribute('name');

				if(!empty($name))
				{
					$types[] = $childNode;
				}
			}
		}

		// complex and simple types
		$definedTypes = array();
		foreach($types as $type)
		{
			$name = $type->getAttribute('name');
			if(!isset($definedTypes[$name]) && in_array($type->nodeName, array('xs:complexType', 'xs:simpleType')))
			{
				$definedTypes[$name] = $type;
			}
		}

		// create elements for each operation
		$dom     = new DOMDocument();
		$methods = array(View::METHOD_GET, View::METHOD_POST, View::METHOD_PUT, View::METHOD_DELETE);

		foreach($methods as $method)
		{
			if($views->has($method))
			{
				if($views->has($method | View::TYPE_REQUEST))
				{
					$name = $this->getMethodNameByModifier($method);
				}
				else if($views->has($method | View::TYPE_RESPONSE))
				{
					$name = $this->getMethodNameByModifier($method);
				}

				// request
				if($views->has($method | View::TYPE_REQUEST))
				{
					$type = $views->get($method | View::TYPE_REQUEST)->getDefinition()->getName();
				}
				else
				{
					$type = 'void';
				}

				$element = $dom->createElement('xs:element');
				$element->setAttribute('name', $name . 'Request');
				$element->setAttribute('type', 'tns:' . $type);

				$elements[$name . 'Request'] = $element;

				// response
				if($views->has($method | View::TYPE_RESPONSE))
				{
					$type = $views->get($method | View::TYPE_RESPONSE)->getDefinition()->getName();
				}
				else
				{
					$type = 'void';
				}

				$element = $dom->createElement('xs:element');
				$element->setAttribute('name', $name . 'Response');
				$element->setAttribute('type', 'tns:' . $type);

				$elements[$name . 'Response'] = $element;
			}
		}

		$element = $dom->createElement('xs:element');
		$element->setAttribute('name', 'exceptionRecord');
		$element->setAttribute('type', 'tns:' . 'fault');

		$elements['faultResponse'] = $element;

		return array_merge(array_values($elements), array_values($definedTypes));
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
				$operation  = new Operation($methodName . ucfirst($entityName));
				$operation->setMethod(strtoupper($methodName));

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

	protected function getGenerator()
	{
		if($this->version == self::VERSION_1)
		{
			return new Generator\Version1();
		}
		else
		{
			throw new InvalidArgumentException('Unknown version');
		}
	}

	protected function getDefaultSchema()
	{
		return <<<XML
<?xml version="1.0"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
	<xs:complexType name="fault">
		<xs:sequence>
			<xs:element name="success" type="xs:boolean" minOccurs="1" maxOccurs="1"/>
			<xs:element name="title" type="xs:string" minOccurs="1" maxOccurs="1"/>
			<xs:element name="message" type="xs:string" minOccurs="1" maxOccurs="1"/>
			<xs:element name="trace" type="xs:string" minOccurs="0" maxOccurs="1"/>
			<xs:element name="context" type="xs:string" minOccurs="0" maxOccurs="1"/>
		</xs:sequence>
	</xs:complexType>
	<xs:complexType name="void">
		<xs:sequence>
		</xs:sequence>
	</xs:complexType>
</xs:schema>
XML;
	}

	protected function getElementsByTagName(DOMElement $element, $nodeName)
	{
		$result = array();
		foreach($element->childNodes as $childNode)
		{
			if($childNode->nodeType == XML_ELEMENT_NODE && $childNode->nodeName == $nodeName)
			{
				$result[] = $childNode;
			}
		}

		return $result;
	}
}
