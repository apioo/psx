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
 * RoutingController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RoutingController extends ApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Framework\Loader\RoutingParserInterface
     */
    protected $routingParser;

    public function onGet()
    {
        parent::onGet();

        $this->setBody(array(
            'routings' => $this->getRoutings(),
        ));
    }

    protected function getRoutings()
    {
        $result   = array();
        $routings = $this->routingParser->getCollection()->getAll();

        foreach ($routings as $routing) {
            list($methods, $path, $source) = $routing;

            $result[] = Record::fromArray([
                'methods' => $methods,
                'path'    => $path,
                'source'  => $source,
            ]);
        }

        return $result;
    }
}
