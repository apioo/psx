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

namespace PSX\Framework\Tests\Controller\Foo\Application\SchemaApi;

use PSX\Api\Resource;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Schema\Property;
use PSX\Framework\Loader\Context;

/**
 * PropertyDocumentationController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PropertyDocumentationController extends SchemaApiAbstract
{
    use PropertyControllerTrait;

    /**
     * @Inject
     * @var \PSX\Schema\SchemaManager
     */
    protected $schemaManager;

    public function getDocumentation($version = null)
    {
        $resource = new Resource(Resource::STATUS_ACTIVE, $this->context->get(Context::KEY_PATH));

        $resource->addPathParameter(Property::getInteger('id'));

        $resource->addMethod(Resource\Factory::getMethod('GET')
            ->addQueryParameter(Property::getInteger('type'))
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Framework\Tests\Controller\Foo\Schema\Property'))
        );

        $resource->addMethod(Resource\Factory::getMethod('POST')
            ->setRequest($this->schemaManager->getSchema('PSX\Framework\Tests\Controller\Foo\Schema\Property'))
            ->addResponse(200, $this->schemaManager->getSchema('PSX\Framework\Tests\Controller\Foo\Schema\Property'))
        );

        return $resource;
    }
}
