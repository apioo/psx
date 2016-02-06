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

namespace PSX\Api\Resource\Generator;

use DOMDocument;
use DOMElement;
use PSX\Api\Resource;
use PSX\Api\Resource\Generator\Wsdl\Operation;
use PSX\Api\Resource\GeneratorAbstract;
use PSX\Data\SchemaInterface;

/**
 * Generates an WSDL 1.1 representation of an API view
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Wsdl extends GeneratorAbstract
{
    protected $name;
    protected $endpoint;
    protected $targetNamespace;

    public function __construct($name, $endpoint, $targetNamespace)
    {
        $this->name            = $name;
        $this->endpoint        = $endpoint;
        $this->targetNamespace = $targetNamespace;
    }

    public function generate(Resource $resource)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $wsdl = $dom->createElement('wsdl:definitions');
        $wsdl->setAttribute('name', $this->name);
        $wsdl->setAttribute('targetNamespace', $this->targetNamespace);
        $wsdl->setAttribute('xmlns:tns', $this->targetNamespace);
        $wsdl->setAttribute('xmlns:soap', 'http://schemas.xmlsoap.org/wsdl/soap/');
        $wsdl->setAttribute('xmlns:wsdl', 'http://schemas.xmlsoap.org/wsdl/');

        $operations = $this->getOperations($resource);

        $this->appendTypes($wsdl, $resource);
        $this->appendMessages($wsdl, $operations);
        $this->appendPortTypes($wsdl, $operations);
        $this->appendBindings($wsdl, $operations);
        $this->appendServices($wsdl);

        $dom->appendChild($wsdl);

        return $dom->saveXML();
    }

    protected function getOperations(Resource $resource)
    {
        $operations = array();
        $methods    = $resource->getMethods();

        foreach ($methods as $method) {
            $request  = $method->getRequest();
            $response = $this->getSuccessfulResponse($method);

            if ($request instanceof SchemaInterface) {
                $entityName = $request->getDefinition()->getName();
            } elseif ($response instanceof SchemaInterface) {
                $entityName = $response->getDefinition()->getName();
            }

            $operation = new Operation(strtolower($method->getName()) . ucfirst($entityName));
            $operation->setPath($resource->getPath());
            $operation->setMethod($method->getName());

            if ($request instanceof SchemaInterface) {
                $operation->setIn($request->getDefinition()->getName());
            }

            if ($response instanceof SchemaInterface) {
                $operation->setOut($response->getDefinition()->getName());
            }

            if ($operation->hasOperation()) {
                $operations[] = $operation;
            }
        }

        return $operations;
    }

    protected function appendTypes(DOMElement $element, Resource $resource)
    {
        $types  = $element->ownerDocument->createElement('wsdl:types');
        $schema = $element->ownerDocument->createElementNS('http://www.w3.org/2001/XMLSchema', 'xs:schema');
        $schema->setAttribute('targetNamespace', $this->targetNamespace);
        $schema->setAttribute('elementFormDefault', 'qualified');
        $schema->setAttribute('xmlns:tns', $this->targetNamespace);

        $xsdGenerator = new Xsd($this->targetNamespace);
        $xsdGenerator->appendSchema($schema, $resource);

        $types->appendChild($schema);

        $element->appendChild($types);
    }

    public function appendMessages(DOMElement $element, array $operations)
    {
        foreach ($operations as $operation) {
            // input
            $interface = $element->ownerDocument->createElement('wsdl:message');
            $interface->setAttribute('name', $operation->getName() . 'Input');

            $part = $element->ownerDocument->createElement('wsdl:part');
            $part->setAttribute('name', 'body');
            $part->setAttribute('element', 'tns:' . strtolower($operation->getMethod()) . 'Request');

            $interface->appendChild($part);

            $element->appendChild($interface);

            // output
            $interface = $element->ownerDocument->createElement('wsdl:message');
            $interface->setAttribute('name', $operation->getName() . 'Output');

            $part = $element->ownerDocument->createElement('wsdl:part');
            $part->setAttribute('name', 'body');
            $part->setAttribute('element', 'tns:' . strtolower($operation->getMethod()) . 'Response');

            $interface->appendChild($part);

            $element->appendChild($interface);
        }

        // default types
        $interface = $element->ownerDocument->createElement('wsdl:message');
        $interface->setAttribute('name', 'faultOutput');

        $part = $element->ownerDocument->createElement('wsdl:part');
        $part->setAttribute('name', 'body');
        $part->setAttribute('element', 'tns:' . 'error');

        $interface->appendChild($part);

        $element->appendChild($interface);
    }

    public function appendPortTypes(DOMElement $element, array $operations)
    {
        $portType = $element->ownerDocument->createElement('wsdl:portType');
        $portType->setAttribute('name', $this->name . 'PortType');

        foreach ($operations as $operation) {
            $oper = $element->ownerDocument->createElement('wsdl:operation');
            $oper->setAttribute('name', $operation->getName());

            // input
            $input = $element->ownerDocument->createElement('wsdl:input');
            $input->setAttribute('message', 'tns:' . $operation->getName() . 'Input');

            $oper->appendChild($input);

            // output
            $output = $element->ownerDocument->createElement('wsdl:output');
            $output->setAttribute('message', 'tns:' . $operation->getName() . 'Output');

            $oper->appendChild($output);

            $output = $element->ownerDocument->createElement('wsdl:fault');
            $output->setAttribute('message', 'tns:' . 'faultOutput');
            $output->setAttribute('name', 'SoapFaultException');
    
            $oper->appendChild($output);

            $portType->appendChild($oper);
        }

        $element->appendChild($portType);
    }

    public function appendBindings(DOMElement $element, array $operations)
    {
        $binding = $element->ownerDocument->createElement('wsdl:binding');
        $binding->setAttribute('name', $this->name . 'Binding');
        $binding->setAttribute('type', 'tns:' . $this->name . 'PortType');

        $soapBinding = $element->ownerDocument->createElement('soap:binding');
        $soapBinding->setAttribute('style', 'document');
        $soapBinding->setAttribute('transport', 'http://schemas.xmlsoap.org/soap/http');

        $binding->appendChild($soapBinding);

        foreach ($operations as $operation) {
            $oper = $element->ownerDocument->createElement('wsdl:operation');
            $oper->setAttribute('name', $operation->getName());

            $soapOperation = $element->ownerDocument->createElement('soap:operation');
            $soapOperation->setAttribute('soapAction', $operation->getPath() . '#' . $operation->getMethod());

            $oper->appendChild($soapOperation);

            // input
            $input    = $element->ownerDocument->createElement('wsdl:input');
            $soapBody = $element->ownerDocument->createElement('soap:body');
            $soapBody->setAttribute('use', 'literal');

            $input->appendChild($soapBody);

            $oper->appendChild($input);

            // output
            $output   = $element->ownerDocument->createElement('wsdl:output');
            $soapBody = $element->ownerDocument->createElement('soap:body');
            $soapBody->setAttribute('use', 'literal');

            $output->appendChild($soapBody);

            $oper->appendChild($output);

            // fault
            $output   = $element->ownerDocument->createElement('wsdl:fault');
            $output->setAttribute('name', 'SoapFaultException');
            $soapBody = $element->ownerDocument->createElement('soap:body');
            $soapBody->setAttribute('use', 'literal');
            $soapBody->setAttribute('name', 'SoapFaultException');

            $output->appendChild($soapBody);

            $oper->appendChild($output);

            $binding->appendChild($oper);
        }

        $element->appendChild($binding);
    }

    public function appendServices(DOMElement $element)
    {
        $service = $element->ownerDocument->createElement('wsdl:service');
        $service->setAttribute('name', $this->name . 'Service');

        $port = $element->ownerDocument->createElement('wsdl:port');
        $port->setAttribute('name', $this->name . 'Port');
        $port->setAttribute('binding', 'tns:' . $this->name . 'Binding');

        $address = $element->ownerDocument->createElement('soap:address');
        $address->setAttribute('location', $this->endpoint);

        $port->appendChild($address);

        $service->appendChild($port);

        $element->appendChild($service);
    }
}
