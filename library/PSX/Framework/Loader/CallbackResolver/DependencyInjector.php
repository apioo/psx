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

namespace PSX\Framework\Loader\CallbackResolver;

use PSX\Framework\Dispatch\ControllerFactoryInterface;
use PSX\Framework\Loader\CallbackResolverInterface;
use PSX\Framework\Loader\Context;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * DependencyInjector
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DependencyInjector implements CallbackResolverInterface
{
    protected $factory;

    public function __construct(ControllerFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function resolve(RequestInterface $request, ResponseInterface $response, Context $context)
    {
        $source = $context->get(Context::KEY_SOURCE);

        if (strpos($source, '::') !== false) {
            list($className, $method) = explode('::', $source, 2);
        } else {
            $className = $source;
            $method    = null;
        }

        $context->set(Context::KEY_CLASS, $className);
        $context->set(Context::KEY_METHOD, $method);

        return $this->factory->getController($className, $request, $response, $context);
    }
}
