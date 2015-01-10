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

namespace PSX\Http\Handler;

use Closure;
use DOMDocument;
use DOMElement;
use DOMCdataSection;
use PSX\Exception;
use PSX\Http;
use PSX\Http\Options;
use PSX\Http\Request;
use PSX\Http\Response;

/**
 * Handler wich captures all http requests into an xml file wich can be loaded 
 * by the Mock handler
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class MockCapture extends Curl
{
	protected $file;

	public function __construct($file)
	{
		parent::__construct();

		$this->file = $file;
	}

	public function request(Request $request, Options $options)
	{
		$response = parent::request($request, $options);

		$dom = new DOMDocument();
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;
		$dom->load($this->file);

		$rootElement = $dom->documentElement;

		if(!$rootElement instanceof DOMElement)
		{
			$rootElement = $dom->createElement('resources');
		}

		$resources = $rootElement->getElementsByTagName('resource');
		$replaced  = false;

		foreach($resources as $resource)
		{
			$method = $resource->getElementsByTagName('method')->item(0);
			$url    = $resource->getElementsByTagName('url')->item(0);

			if($method instanceof DOMElement && $url instanceof DOMElement)
			{
				if($method->nodeValue == $request->getMethod() && $url->nodeValue == $request->getUrl()->toString())
				{
					$element = $resource->getElementsByTagName('response')->item(0);

					if($element instanceof DOMElement)
					{
						$element->nodeValue = base64_encode((string) $response);
					}
					else
					{
						$element = $dom->createElement('response');
						$element->appendChild($dom->createTextNode(base64_encode((string) $response)));
						$resource->appendChild($element);
					}

					$replaced = true;
				}
			}
		}

		if($replaced === false)
		{
			$resource = $dom->createElement('resource');

			$element = $dom->createElement('method');
			$element->appendChild($dom->createTextNode($request->getMethod()));
			$resource->appendChild($element);

			$element = $dom->createElement('url');
			$element->appendChild($dom->createTextNode($request->getUrl()->toString()));
			$resource->appendChild($element);

			$element = $dom->createElement('response');
			$element->appendChild($dom->createTextNode(base64_encode((string) $response)));
			$resource->appendChild($element);

			$rootElement->appendChild($resource);
		}

		$dom->appendChild($rootElement);
		$dom->save($this->file);

		return $response;
	}
}
