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

namespace PSX\Cache;

use Doctrine\Common\Cache\CacheProvider;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Cache pool implementation which uses the doctrine cache system
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Pool implements CacheItemPoolInterface
{
    protected $handler;
    protected $items;

    public function __construct(CacheProvider $handler)
    {
        $this->handler = $handler;
        $this->items   = [];
    }

    public function setHandler(CacheProvider $handler)
    {
        $this->handler = $handler;
    }

    public function getItem($key)
    {
        $data = $this->handler->fetch($key);

        if ($data !== false) {
            return new Item($key, $data, true);
        } else {
            return new Item($key, null, false);
        }
    }

    public function getItems(array $keys = array())
    {
        $result = [];
        $data   = $this->handler->fetchMultiple($keys);

        foreach ($keys as $key) {
            if (isset($data[$key]) && $data[$key] !== false) {
                $result[] = new Item($key, $data[$key], true);
            } else {
                $result[] = new Item($key, null, false);
            }
        }

        return $result;
    }

    public function hasItem($key)
    {
        return $this->handler->contains($key);
    }

    public function clear()
    {
        return $this->handler->flushAll();
    }

    public function deleteItem($key)
    {
        return $this->handler->delete($key);
    }

    public function deleteItems(array $keys)
    {
        $result = true;
        foreach ($keys as $key) {
            if ($this->handler->delete($key) === false) {
                $result = false;
            }
        }

        return $result;
    }

    public function save(CacheItemInterface $item)
    {
        return $this->handler->save($item->getKey(), $item->get(), $item->getTtl());
    }

    public function saveDeferred(CacheItemInterface $item)
    {
        $this->items[] = $item;

        return true;
    }

    public function commit()
    {
        $result = true;
        foreach ($this->items as $item) {
            if ($this->save($item) === false) {
                $result = false;
            }
        }

        $this->items = [];

        return $result;
    }
}
