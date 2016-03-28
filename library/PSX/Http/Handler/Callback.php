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

namespace PSX\Http\Handler;

use Closure;
use PSX\Http\HandlerInterface;
use PSX\Http\Options;
use PSX\Http\RequestInterface;
use PSX\Http\Response;
use PSX\Http\ResponseParser;

/**
 * Callback
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Callback implements HandlerInterface
{
    protected $callback;

    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
    }

    public function request(RequestInterface $request, Options $options)
    {
        try {
            $response = call_user_func_array($this->callback, array($request, $options));

            if ($response instanceof Response) {
                return $response;
            } else {
                return ResponseParser::convert((string) $response);
            }
        } catch (\PHPUnit_Framework_Exception $e) {
            throw $e;
        } catch (\ErrorException $e) {
            throw $e;
        }
    }
}
