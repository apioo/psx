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
 * Version2
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Version2 implements GeneratorInterface
{
	protected $document;
	protected $targetNamespace;
	protected $schemaNamespace;

	public function generate($targetNamespace, $schemaNamespace, array $types, array $operations)
	{
		$this->document        = new DOMDocument();
		$this->targetNamespace = $targetNamespace;
		$this->schemaNamespace = $schemaNamespace;

		$description = $this->document->createElement('description');
		$description->setAttribute('targetNamespace', $this->targetNamespace);
		$description->setAttribute('xmlns', 'http://www.w3.org/ns/wsdl');
		$description->setAttribute('xmlns:tns', $this->targetNamespace);
		$description->setAttribute('xmlns:sns', $this->schemaNamespace);
		$description->setAttribute('xmlns:wsoap', 'http://www.w3.org/ns/wsdl/soap');
		$description->setAttribute('xmlns:whttp', 'http://www.w3.org/ns/wsdl/http');
		$description->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');

		$this->appendTypes($description, $types);
		$this->appendInterfaces($description, $operations);
		$this->appendBindings($description, $operations);
		$this->appendServices($description, $operations);

		$this->document->appendChild($description);

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

	public function appendInterfaces(DOMElement $element, array $operations)
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

			$interface = $this->document->createElement('interface');
			$interface->setAttribute('name', $operation->getName() . 'Interface');

			$oper = $this->document->createElement('operation');
			$oper->setAttribute('name', $operation->getName());
			$oper->setAttribute('pattern', $pattern);
			$oper->setAttribute('style', 'http://www.w3.org/ns/wsdl/style/iri');

			if($operation->hasIn())
			{
				$input = $this->document->createElement('input');
				$input->setAttribute('messageLabel', 'In');
				$input->setAttribute('element', 'sns:' . $operation->getIn());

				$oper->appendChild($input);
			}

			if($operation->hasOut())
			{
				$output = $this->document->createElement('output');
				$output->setAttribute('messageLabel', 'Out');
				$output->setAttribute('element', 'sns:' . $operation->getOut());

				$oper->appendChild($output);
			}

			$interface->appendChild($oper);

			$element->appendChild($interface);
		}
	}

	public function appendBindings(DOMElement $element, array $operations)
	{
		foreach($operations as $operation)
		{
			$binding = $this->document->createElement('binding');
			$binding->setAttribute('name', $operation->getName() . 'Binding');
			$binding->setAttribute('interface', 'tns:' . $operation->getName() . 'Interface');
			$binding->setAttribute('type', 'http://www.w3.org/ns/wsdl/soap');
			$binding->setAttribute('wsoap:protocol', 'http://www.w3.org/2003/05/soap/bindings/HTTP/');

			$oper = $this->document->createElement('operation');
			$oper->setAttribute('ref', 'tns:' . $operation->getName());
			$oper->setAttribute('wsoap:mep', 'http://www.w3.org/2003/05/soap/mep/soap-response');

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
			$service->setAttribute('interface', 'tns:' . $operation->getName() . 'Interface');

			$address = $this->document->createElement('endpoint');
			$address->setAttribute('name', $operation->getName() . 'Endpoint');
			$address->setAttribute('binding', 'tns:' . $operation->getName() . 'Binding');
			$address->setAttribute('address', $operation->getEndpoint());

			$service->appendChild($address);

			$element->appendChild($service);
		}
	}
}
