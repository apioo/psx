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

namespace PSX\Framework\Tests\Loader;

use PSX\Framework\Controller\ControllerAbstract;
use PSX\Framework\Loader\Context;
use PSX\Http\Request;
use PSX\Http\Response;

/**
 * ProbeController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ProbeController extends ControllerAbstract
{
    protected $methodsCalled = array();

    public function __construct(Request $request, Response $response, Context $context)
    {
        parent::__construct($request, $response, $context);

        $this->methodsCalled[] = __METHOD__;
    }

    public function getStage()
    {
        $this->methodsCalled[] = __METHOD__;

        return parent::getStage();
    }

    public function getPreFilter()
    {
        $this->methodsCalled[] = __METHOD__;

        return parent::getPreFilter();
    }

    public function getPostFilter()
    {
        $this->methodsCalled[] = __METHOD__;

        return parent::getPostFilter();
    }

    public function onLoad()
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function onGet()
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function onPost()
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function onPut()
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function onDelete()
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function processResponse()
    {
        $this->methodsCalled[] = __METHOD__;

        return parent::processResponse();
    }

    public function doIndex()
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function doShowDetails()
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function doInsert()
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function doInsertNested()
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function doUpdate()
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function doUpdateNested()
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function doDelete()
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function doDeleteNested()
    {
        $this->methodsCalled[] = __METHOD__;
    }

    public function getMethodsCalled()
    {
        return $this->methodsCalled;
    }

    public function getFragments()
    {
        return $this->uriFragments;
    }
}
