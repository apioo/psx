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

namespace PSX\Controller\Tool;

use PSX\Api\DocumentationInterface;
use PSX\Api\Resource;
use PSX\Api\Resource\Generator;
use PSX\ControllerAbstract;
use PSX\Http\Exception as HttpException;
use PSX\Util\ApiGeneration;

/**
 * WsdlGeneratorController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class WsdlGeneratorController extends ControllerAbstract
{
    /**
     * @Inject
     * @var \PSX\Api\Resource\ListingInterface
     */
    protected $resourceListing;

    public function onGet()
    {
        parent::onGet();

        $version = $this->getUriFragment('version');
        $path    = $this->getUriFragment('path');
        $doc     = $this->resourceListing->getDocumentation($path);

        if ($doc instanceof DocumentationInterface) {
            if ($version == '*') {
                $version = $doc->getLatestVersion();
            }

            $resource = $doc->getResource($version);

            if (!$resource instanceof Resource) {
                throw new HttpException\NotFoundException('Given version is not available');
            }

            $path  = ltrim($resource->getPath(), '/');
            $title = $resource->getTitle();

            if (empty($title)) {
                $title = ApiGeneration::generateTitleFromRoute($path);
            }

            $endpoint        = $this->config['psx_url'] . '/' . $this->config['psx_dispatch'] . $path;
            $targetNamespace = $this->config['psx_soap_namespace'];

            $this->response->setHeader('Content-Type', 'text/xml');

            $generator = new Generator\Wsdl($title, $endpoint, $targetNamespace);

            $this->setBody($generator->generate($resource));
        } else {
            throw new HttpException\NotFoundException('Invalid resource');
        }
    }
}
