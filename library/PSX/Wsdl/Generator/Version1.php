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
	protected $name;
	protected $endpoint;
	protected $targetNamespace;

	public function generate($name, $endpoint, $targetNamespace, array $types, array $operations)
	{
		$this->document        = new DOMDocument();
		$this->name            = $name;
		$this->endpoint        = $endpoint;
		$this->targetNamespace = $targetNamespace;

		$definition = $this->document->createElement('wsdl:definitions');
		$definition->setAttribute('name', $this->name);
		$definition->setAttribute('targetNamespace', $this->targetNamespace);
		$definition->setAttribute('xmlns:tns', $this->targetNamespace);
		$definition->setAttribute('xmlns:soap', 'http://schemas.xmlsoap.org/wsdl/soap/');
		$definition->setAttribute('xmlns:wsdl', 'http://schemas.xmlsoap.org/wsdl/');

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
		$types  = $this->document->createElement('wsdl:types');
		$schema = $this->document->createElementNS('http://www.w3.org/2001/XMLSchema', 'xs:schema');
		$schema->setAttribute('targetNamespace', $this->targetNamespace);
		$schema->setAttribute('elementFormDefault', 'qualified');
		$schema->setAttribute('xmlns:tns', $this->targetNamespace);

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
			// input
			$interface = $this->document->createElement('wsdl:message');
			$interface->setAttribute('name', $operation->getName() . 'Input');

			$part = $this->document->createElement('wsdl:part');
			$part->setAttribute('name', 'body');
			$part->setAttribute('element', 'tns:' . strtolower($operation->getMethod()) . 'Request');

			$interface->appendChild($part);

			$element->appendChild($interface);

			// output
			$interface = $this->document->createElement('wsdl:message');
			$interface->setAttribute('name', $operation->getName() . 'Output');

			$part = $this->document->createElement('wsdl:part');
			$part->setAttribute('name', 'body');
			$part->setAttribute('element', 'tns:' . strtolower($operation->getMethod()) . 'Response');

			$interface->appendChild($part);

			$element->appendChild($interface);
		}

		// default types
		$interface = $this->document->createElement('wsdl:message');
		$interface->setAttribute('name', 'faultOutput');

		$part = $this->document->createElement('wsdl:part');
		$part->setAttribute('name', 'body');
		$part->setAttribute('element', 'tns:' . 'exceptionRecord');

		$interface->appendChild($part);

		$element->appendChild($interface);
	}

	public function appendPortTypes(DOMElement $element, array $operations)
	{
		$portType = $this->document->createElement('wsdl:portType');
		$portType->setAttribute('name', $this->name . 'PortType');

		foreach($operations as $operation)
		{
			$oper = $this->document->createElement('wsdl:operation');
			$oper->setAttribute('name', $operation->getName());

			// input
			$input = $this->document->createElement('wsdl:input');
			$input->setAttribute('message', 'tns:' . $operation->getName() . 'Input');

			$oper->appendChild($input);

			// output
			$output = $this->document->createElement('wsdl:output');
			$output->setAttribute('message', 'tns:' . $operation->getName() . 'Output');

			$oper->appendChild($output);

			$output = $this->document->createElement('wsdl:fault');
			$output->setAttribute('message', 'tns:' . 'faultOutput');
			$output->setAttribute('name', 'SoapFaultException');
	
			$oper->appendChild($output);

			$portType->appendChild($oper);
		}

		$element->appendChild($portType);
	}

	public function appendBindings(DOMElement $element, array $operations)
	{
		$binding = $this->document->createElement('wsdl:binding');
		$binding->setAttribute('name', $this->name . 'Binding');
		$binding->setAttribute('type', 'tns:' . $this->name . 'PortType');

		$soapBinding = $this->document->createElement('soap:binding');
		$soapBinding->setAttribute('style', 'document');
		$soapBinding->setAttribute('transport', 'http://schemas.xmlsoap.org/soap/http');

		$binding->appendChild($soapBinding);

		foreach($operations as $operation)
		{
			$oper = $this->document->createElement('wsdl:operation');
			$oper->setAttribute('name', $operation->getName());

			$soapOperation = $this->document->createElement('soap:operation');
			$soapOperation->setAttribute('soapAction', $this->targetNamespace . '/' . $operation->getName() . '#' . $operation->getMethod());

			$oper->appendChild($soapOperation);

			// input
			$input    = $this->document->createElement('wsdl:input');
			$soapBody = $this->document->createElement('soap:body');
			$soapBody->setAttribute('use', 'literal');

			$input->appendChild($soapBody);

			$oper->appendChild($input);

			// output
			$output   = $this->document->createElement('wsdl:output');
			$soapBody = $this->document->createElement('soap:body');
			$soapBody->setAttribute('use', 'literal');

			$output->appendChild($soapBody);

			$oper->appendChild($output);

			// fault
			$output   = $this->document->createElement('wsdl:fault');
			$output->setAttribute('name', 'SoapFaultException');
			$soapBody = $this->document->createElement('soap:body');
			$soapBody->setAttribute('use', 'literal');
			$soapBody->setAttribute('name', 'SoapFaultException');

			$output->appendChild($soapBody);

			$oper->appendChild($output);

			$binding->appendChild($oper);
		}

		$element->appendChild($binding);
	}

	public function appendServices(DOMElement $element, array $operations)
	{
		$service = $this->document->createElement('wsdl:service');
		$service->setAttribute('name', $this->name . 'Service');

		$port = $this->document->createElement('wsdl:port');
		$port->setAttribute('name', $this->name . 'Port');
		$port->setAttribute('binding', 'tns:' . $this->name . 'Binding');

		$address = $this->document->createElement('soap:address');
		$address->setAttribute('location', $this->endpoint);

		$port->appendChild($address);

		$service->appendChild($port);

		$element->appendChild($service);
	}
}
