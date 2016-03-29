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
use PSX\Api\Util\Inflection;
use PSX\Data\Exporter;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Data\WriterInterface;
use PSX\Http\Exception as HttpException;
use PSX\Model\Swagger\ResourceListing;
use PSX\Model\Swagger\ResourceObject;

/**
 * SwaggerController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SwaggerController extends ControllerAbstract
{
    /**
     * @Inject
     * @var \PSX\Api\ListingInterface
     */
    protected $resourceListing;

    /**
     * @Inject
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $annotationReader;

    public function doIndex()
    {
        $resourceListing = new ResourceListing('1.0');
        $resources       = $this->resourceListing->getResourceIndex();

        foreach ($resources as $resource) {
            $path = '/*';
            $path.= Inflection::transformRoutePlaceholder($resource->getPath());

            $resourceListing->addResource(new ResourceObject($path));
        }

        $this->setBody($resourceListing, WriterInterface::JSON);
    }

    public function doDetail()
    {
        $version  = (int) $this->getUriFragment('version');
        $resource = $this->resourceListing->getResource($this->getUriFragment('path'), $version);

        if ($resource instanceof Resource) {
            $baseUri         = $this->config['psx_url'] . '/' . $this->config['psx_dispatch'];
            $targetNamespace = $this->config['psx_json_namespace'];

            $generator = new Generator\Swagger(new Exporter\Popo($this->annotationReader), $version, $baseUri, $targetNamespace);
            $swagger   = $generator->generate($resource);

            $this->setHeader('Content-Type', 'application/json');
            $this->setBody($swagger);
        } else {
            throw new HttpException\NotFoundException('Invalid resource');
        }
    }
}
