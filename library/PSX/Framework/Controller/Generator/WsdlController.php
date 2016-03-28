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

namespace PSX\Framework\Controller\Generator;

use PSX\Api\Resource;
use PSX\Api\Generator;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Http\Exception as HttpException;
use PSX\Api\Util\Inflection;
use RuntimeException;

/**
 * WsdlController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class WsdlController extends ControllerAbstract
{
    /**
     * @Inject
     * @var \PSX\Api\ListingInterface
     */
    protected $resourceListing;

    /**
     * @Inject
     * @var \PSX\Framework\Loader\ReverseRouter
     */
    protected $reverseRouter;

    public function onGet()
    {
        parent::onGet();

        $version  = (int) $this->getUriFragment('version');
        $resource = $this->resourceListing->getResource($this->getUriFragment('path'), $version);

        if ($resource instanceof Resource) {
            $path  = ltrim($resource->getPath(), '/');
            $title = $resource->getTitle();

            if (empty($title)) {
                $title = Inflection::generateTitleFromRoute($path);
            }

            $endpoint = $this->reverseRouter->getUrl('PSX\Framework\Controller\Proxy\SoapController');

            if (empty($endpoint)) {
                throw new RuntimeException('Could not find soap proxy controller');
            }

            $generator = new Generator\Wsdl($title, $endpoint, $this->config['psx_soap_namespace']);
            $wsdl      = $generator->generate($resource);

            $this->setHeader('Content-Type', 'text/xml');
            $this->setBody($wsdl);
        } else {
            throw new HttpException\NotFoundException('Invalid resource');
        }
    }
}
