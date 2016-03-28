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

namespace PSX\Framework\Loader\RoutingParser;

use PSX\Framework\Loader\RoutingCollection;
use PSX\Framework\Loader\RoutingParserInterface;

/**
 * RoutingFile
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RoutingFile implements RoutingParserInterface
{
    protected $file;

    protected $_collection;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function getCollection()
    {
        if ($this->_collection === null) {
            $collection = new RoutingCollection();
            $lines      = file($this->file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {
                $line = trim(str_replace("\t", ' ', $line));

                if (!empty($line) && $line[0] != '#') {
                    $line    = preg_replace('/([\s]{1,})/', ' ', $line);
                    $parts   = explode(' ', $line);

                    if ($parts[0] == 'ANY') {
                        $parts[0] = 'GET|HEAD|POST|PUT|DELETE|PATCH';
                    }

                    $allowed = isset($parts[0]) ? explode('|', $parts[0]) : array();
                    $path    = isset($parts[1]) ? $parts[1] : null;
                    $class   = isset($parts[2]) ? $parts[2] : null;

                    if (!empty($allowed) && !empty($path) && !empty($class)) {
                        $collection->add($allowed, $path, $class);
                    }
                }
            }

            $this->_collection = $collection;
        }

        return $this->_collection;
    }
}
