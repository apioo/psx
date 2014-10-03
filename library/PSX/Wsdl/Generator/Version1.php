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

namespace PSX\Wsdl\Generator;

use DOMDocument;
use DOMElement;
use PSX\Wsdl\GeneratorInterface;

/**
 * Version1
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Version1 implements GeneratorInterface
{
	protected $document;
	protected $targetNamespace;
	protected $schemaNamespace;

	public function generate($targetNamespace, $schemaNamespace, array $types, array $operations)
	{
		$this->document        = new DOMDocument();
		$this->targetNamespace = $targetNamespace;
		$this->schemaNamespace = $schemaNamespace;

		$definition = $this->document->createElement('definitions');
		$definition->setAttribute('name', 'psx');
		$definition->setAttribute('targetNamespace', $this->targetNamespace);
		$definition->setAttribute('xmlns', 'http://schemas.xmlsoap.org/wsdl/');
		$definition->setAttribute('xmlns:tns', $this->targetNamespace);
		$definition->setAttribute('xmlns:sns', $this->schemaNamespace);
		$definition->setAttribute('xmlns:soap', 'http://schemas.xmlsoap.org/wsdl/soap/');

		$this->appendTypes($definition, $types);
		$this->appendMessages($definition, $operations);
		$this->appendPortTypes($definition, $operations);
		$this->appendBindings($definition, $operations);
		$this->appendServices($definition, $operations);

		$this->document->appendChild($definition);

		return $this->document;
	}

	public function appendTypes(DOMElement $element, array $operations)
	{
		$types  = $this->document->createElement('types');
		$schema = $this->document->createElementNS('http://www.w3.org/2001/XMLSchema', 'xs:schema');
		$schema->setAttribute('targetNamespace', $this->schemaNamespace);
		$schema->setAttribute('xmlns', $this->schemaNamespace);

		foreach($operations as $type)
		{
			$node = $this->document->importNode($type, true);

			$schema->appendChild($node);
		}

		$types->appendChild($schema);

		$element->appendChild($types);
	}

	public function appendMessages(DOMElement $element, array $operations)
	{
		foreach($operations as $operation)
		{
			if($operation->hasIn())
			{
				$interface = $this->document->createElement('message');
				$interface->setAttribute('name', $operation->getName() . 'Input');

				$part = $this->document->createElement('part');
				$part->setAttribute('name', 'body');
				$part->setAttribute('element', 'sns:' . $operation->getIn());

				$interface->appendChild($part);

				$element->appendChild($interface);
			}

			if($operation->hasOut())
			{
				$interface = $this->document->createElement('message');
				$interface->setAttribute('name', $operation->getName() . 'Output');

				$part = $this->document->createElement('part');
				$part->setAttribute('name', 'body');
				$part->setAttribute('element', 'sns:' . $operation->getOut());

				$interface->appendChild($part);

				$element->appendChild($interface);
			}
		}
	}

	public function appendPortTypes(DOMElement $element, array $operations)
	{
		foreach($operations as $operation)
		{
			if($operation->isInOnly())
			{
				$pattern = 'http://www.w3.org/ns/wsdl/in-only';
			}
			else if($operation->isOutOnly())
			{
				$pattern = 'http://www.w3.org/ns/wsdl/out-only';
			}
			else
			{
				$pattern = 'http://www.w3.org/ns/wsdl/in-out';
			}

			$portType = $this->document->createElement('portType');
			$portType->setAttribute('name', $operation->getName() . 'PortType');

			$oper = $this->document->createElement('operation');
			$oper->setAttribute('name', $operation->getName());

			if($operation->hasIn())
			{
				$input = $this->document->createElement('input');
				$input->setAttribute('message', 'tns:' . $operation->getName() . 'Input');

				$oper->appendChild($input);
			}

			if($operation->hasOut())
			{
				$output = $this->document->createElement('output');
				$output->setAttribute('message', 'tns:' . $operation->getName() . 'Output');

				$oper->appendChild($output);
			}

			$portType->appendChild($oper);

			$element->appendChild($portType);
		}
	}

	public function appendBindings(DOMElement $element, array $operations)
	{
		foreach($operations as $operation)
		{
			$binding = $this->document->createElement('binding');
			$binding->setAttribute('name', $operation->getName() . 'Binding');
			$binding->setAttribute('type', 'tns:' . $operation->getName() . 'PortType');

			$soapBinding = $this->document->createElement('soap:binding');
			$soapBinding->setAttribute('style', 'document');
			$soapBinding->setAttribute('transport', 'http://schemas.xmlsoap.org/soap/http');

			$binding->appendChild($soapBinding);

			$oper = $this->document->createElement('operation');
			$oper->setAttribute('name', $operation->getName());

			$soapOperation = $this->document->createElement('soap:operation');
			$soapOperation->setAttribute('soapAction', $this->targetNamespace . '/' . $operation->getName());

			$oper->appendChild($soapOperation);

			if($operation->hasIn())
			{
				$input    = $this->document->createElement('input');
				$soapBody = $this->document->createElement('soap:body');
				$soapBody->setAttribute('use', 'literal');

				$oper->appendChild($input);
			}

			if($operation->hasOut())
			{
				$output   = $this->document->createElement('output');
				$soapBody = $this->document->createElement('soap:body');
				$soapBody->setAttribute('use', 'literal');

				$oper->appendChild($output);
			}

			$binding->appendChild($oper);

			$element->appendChild($binding);
		}
	}

	public function appendServices(DOMElement $element, array $operations)
	{
		foreach($operations as $operation)
		{
			$service = $this->document->createElement('service');
			$service->setAttribute('name', $operation->getName() . 'Service');

			$port = $this->document->createElement('port');
			$port->setAttribute('name', $operation->getName() . 'Port');
			$port->setAttribute('binding', $operation->getName() . 'Binding');

			$address = $this->document->createElement('soap:address');
			$address->setAttribute('location', $operation->getEndpoint());

			$port->appendChild($address);

			$service->appendChild($port);

			$element->appendChild($service);
		}
	}
}
