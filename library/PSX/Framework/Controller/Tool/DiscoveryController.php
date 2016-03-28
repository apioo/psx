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

namespace PSX\Framework\Controller\Tool;

use PSX\Framework\Controller\ApiAbstract;
use PSX\Data\Record;

/**
 * DiscoveryController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DiscoveryController extends ApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Framework\Loader\ReverseRouter
     */
    protected $reverseRouter;

    public function onGet()
    {
        parent::onGet();

        $links = [];

        $apiPath = $this->reverseRouter->getDispatchUrl();
        if ($apiPath !== null) {
            $links[] = Record::fromArray([
                'rel'  => 'api',
                'href' => $apiPath,
            ]);
        }

        $routingPath = $this->reverseRouter->getUrl('PSX\Framework\Controller\Tool\RoutingController');
        if ($routingPath !== null) {
            $links[] = Record::fromArray([
                'rel'  => 'routing',
                'href' => $routingPath,
            ]);
        }

        $documentationPath = $this->reverseRouter->getUrl('PSX\Framework\Controller\Tool\DocumentationController::doIndex');
        if ($documentationPath !== null) {
            $links[] = Record::fromArray([
                'rel'  => 'documentation',
                'href' => $documentationPath,
            ]);
        }

        $ramlGeneratorPath = $this->reverseRouter->getUrl('PSX\Framework\Controller\Generator\RamlController', ['{version}', '{path}']);
        if ($ramlGeneratorPath !== null) {
            $links[] = Record::fromArray([
                'rel'  => 'raml',
                'href' => $ramlGeneratorPath,
            ]);
        }

        $wsdlGeneratorPath = $this->reverseRouter->getUrl('PSX\Framework\Controller\Generator\WsdlController', ['{version}', '{path}']);
        if ($wsdlGeneratorPath !== null) {
            $links[] = Record::fromArray([
                'rel'  => 'wsdl',
                'href' => $wsdlGeneratorPath,
            ]);
        }

        $swaggerGeneratorPath = $this->reverseRouter->getUrl('PSX\Framework\Controller\Generator\SwaggerController::doDetail', ['{version}', '{path}']);
        if ($swaggerGeneratorPath !== null) {
            $links[] = Record::fromArray([
                'rel'  => 'swagger',
                'href' => $swaggerGeneratorPath,
            ]);
        }

        $this->setBody([
            'links' => $links,
        ]);
    }
}
