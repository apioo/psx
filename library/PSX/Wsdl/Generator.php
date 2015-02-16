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
 * Generates an WSDL 1.1 representation of an API view
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Generator
{
	protected $name;
	protected $endpoint;
	protected $targetNamespace;
	protected $generator;

	protected $types = array();

	public function __construct($name, $endpoint, $targetNamespace, GeneratorInterface $generator = null)
	{
		$this->name            = $name;
		$this->endpoint        = $endpoint;
		$this->targetNamespace = $targetNamespace;
		$this->generator       = $generator ?: new Generator\Version1();
	}

	public function generate(View $view)
	{
		$this->types = array();

		$types      = $this->getTypes($view);
		$operations = $this->getOperations($view);
		
		return $this->generator->generate($this->name, $this->endpoint, $this->targetNamespace, $types, $operations);
	}

	protected function getTypes(View $view)
	{
		$xsdGenerator = new XsdGenerator($this->targetNamespace);
		$types        = array();

		foreach($view as $schema)
		{
			$dom = new DOMDocument();
			$dom->loadXML($xsdGenerator->generate($schema));

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
		$methods = View::getMethods();

		foreach($methods as $method => $methodName)
		{
			if($view->has($method))
			{
				$name = strtolower($methodName);

				// request
				if($view->has($method | View::TYPE_REQUEST))
				{
					$type = $view->get($method | View::TYPE_REQUEST)->getDefinition()->getName();
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
				if($view->has($method | View::TYPE_RESPONSE))
				{
					$type = $view->get($method | View::TYPE_RESPONSE)->getDefinition()->getName();
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

	protected function getOperations(View $view)
	{
		$operations = array();
		$methods    = View::getMethods();

		foreach($methods as $method => $methodName)
		{
			if($view->has($method))
			{
				if($view->has($method | View::TYPE_REQUEST))
				{
					$entityName = $view->get($method | View::TYPE_REQUEST)->getDefinition()->getName();
				}
				else if($view->has($method | View::TYPE_RESPONSE))
				{
					$entityName = $view->get($method | View::TYPE_RESPONSE)->getDefinition()->getName();
				}

				$operation = new Operation(strtolower($methodName) . ucfirst($entityName));
				$operation->setMethod($methodName);

				if($view->has($method | View::TYPE_REQUEST))
				{
					$operation->setIn($view->get($method | View::TYPE_REQUEST)->getDefinition()->getName());
				}

				if($view->has($method | View::TYPE_RESPONSE))
				{
					$operation->setOut($view->get($method | View::TYPE_RESPONSE)->getDefinition()->getName());
				}

				if($operation->hasOperation())
				{
					$operations[] = $operation;
				}
			}
		}

		return $operations;
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
