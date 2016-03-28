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

namespace PSX\Framework\Loader;

use PSX\Http\RequestInterface;

/**
 * LocationFinderInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface LocationFinderInterface
{
    /**
     * Resolves the incoming request to an source. An source is an string which
     * can be resolved to an callback. The source must be added to the context.
     * If the request can not be resolved the method must return null else the
     * given request
     *
     * @param \PSX\Http\RequestInterface $request
     * @param \PSX\Framework\Loader\Context $context
     * @return \PSX\Http\RequestInterface|null
     */
    public function resolve(RequestInterface $request, Context $context);
}
