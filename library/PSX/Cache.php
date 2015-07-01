<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX;

use PSX\Cache\CacheItemInterface;
use PSX\Cache\CacheItemPoolInterface;
use PSX\Cache\Handler;
use PSX\Cache\HandlerInterface;

/**
 * Cache
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Cache implements CacheItemPoolInterface
{
    protected $handler;
    protected $items = array();

    public function __construct(HandlerInterface $handler = null)
    {
        $this->handler = $handler === null ? new Handler\File() : $handler;
    }

    public function getItem($key)
    {
        return $this->handler->load($key);
    }

    public function getItems(array $keys = array())
    {
        $items = array();

        foreach ($keys as $key) {
            $items[] = $this->handler->load($key);
        }

        return $items;
    }

    public function clear()
    {
        return $this->handler->removeAll();
    }

    public function deleteItems(array $keys)
    {
        foreach ($keys as $key) {
            $this->handler->remove($key);
        }

        return $this;
    }

    public function save(CacheItemInterface $item)
    {
        $this->handler->write($item);

        return $this;
    }

    public function saveDeferred(CacheItemInterface $item)
    {
        $this->items[] = $item;

        return $this;
    }

    public function commit()
    {
        foreach ($this->items as $item) {
            $this->handler->write($item);
        }

        $this->items = array();

        return true;
    }
}
