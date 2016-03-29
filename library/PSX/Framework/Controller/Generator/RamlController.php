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
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Http\Exception as HttpException;

/**
 * RamlController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RamlController extends ControllerAbstract
{
    /**
     * @Inject
     * @var \PSX\Api\ListingInterface
     */
    protected $resourceListing;

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

            $baseUri         = $this->config['psx_url'] . '/' . $this->config['psx_dispatch'];
            $targetNamespace = $this->config['psx_json_namespace'];

            $generator = new Generator\Raml($title, $version, $baseUri, $targetNamespace);
            $raml      = $generator->generate($resource);

            $this->setHeader('Content-Type', 'application/raml+yaml');
            $this->setBody($raml);
        } else {
            throw new HttpException\NotFoundException('Invalid resource');
        }
    }
}
