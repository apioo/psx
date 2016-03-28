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

namespace PSX\Framework\Filter;

use PSX\Http\Exception\ForbiddenException;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;

/**
 * Filters an incomming request based on the request IP. Only IPs which are
 * listed in the $allowedIps array can access the application. Note if the IP is
 * not available in the REMOTE_ADDR field of the environment variables (cli) the
 * request can access the application
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class IpFirewall implements FilterInterface
{
    protected $allowedIps;

    public function __construct(array $allowedIps)
    {
        $this->allowedIps = $allowedIps;
    }

    public function handle(RequestInterface $request, ResponseInterface $response, FilterChainInterface $filterChain)
    {
        $ip = $this->getIp();

        if ($ip === null || in_array($ip, $this->allowedIps)) {
            $filterChain->handle($request, $response);
        } else {
            throw new ForbiddenException('Access not allowed');
        }
    }

    protected function getIp()
    {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
    }
}
