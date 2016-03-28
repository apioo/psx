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

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseInterface;
use RuntimeException;

/**
 * FilterChain
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class FilterChain implements FilterChainInterface, LoggerAwareInterface
{
    /**
     * @var array
     */
    protected $filters;

    /**
     * @var \PSX\Framework\Filter\FilterChainInterface
     */
    protected $filterChain;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(array $filters, FilterChainInterface $filterChain = null)
    {
        $this->filters     = $filters;
        $this->filterChain = $filterChain;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(RequestInterface $request, ResponseInterface $response)
    {
        $filter = array_shift($this->filters);

        if ($filter === null) {
            // if we have no filters check whether we have another filter chain
            // which should be called next
            if ($this->filterChain !== null) {
                $this->filterChain->handle($request, $response);
            }
        } elseif ($filter instanceof FilterInterface) {
            if ($this->logger !== null) {
                $this->logger->info('Filter execute ' . get_class($filter));
            }

            $filter->handle($request, $response, $this);
        } elseif (is_callable($filter)) {
            call_user_func_array($filter, array($request, $response, $this));
        } else {
            throw new RuntimeException('Invalid filter value');
        }
    }
}
