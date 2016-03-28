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

namespace PSX\Framework\Config;

use ArrayIterator;
use PSX\Framework\Config\NotFoundException;

/**
 * Simple config class which uses a simple array to store all values. Here an
 * example howto use the class
 * <code>
 * $config = Config::fromFile('configuration.php');
 *
 * echo $config['psx_url'];
 * </code>
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Config extends ArrayIterator
{
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    public function set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    public function get($key)
    {
        return $this->offsetGet($key);
    }

    public function has($key)
    {
        return $this->offsetExists($key);
    }

    public function merge(Config $config)
    {
        return new Config(array_merge($this->getArrayCopy(), $config->getArrayCopy()));
    }

    public static function fromFile($file)
    {
        $config = include($file);

        if (is_array($config)) {
            return new self($config);
        } else {
            throw new NotFoundException('Config file must return an array');
        }
    }
}
