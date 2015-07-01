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

namespace PSX\Cache;

/**
 * The handler is the storage enginge of an cache pool
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface HandlerInterface
{
    /**
     * Returns the cache item with the given key. If it doesnt exist it returns
     * an empty item
     *
     * @param string $key
     * @return \PSX\Cache\CacheItemInterface
     */
    public function load($key);

    /**
     * Writes the cache item
     *
     * @param \PSX\Cache\CacheItemInterface $item
     * @return void
     */
    public function write(CacheItemInterface $item);

    /**
     * Removes the cache item associated with the key
     *
     * @param string $key
     * @return void
     */
    public function remove($key);

    /**
     * Removes all entries. Returns whether all entries were successful removed
     *
     * @return boolean
     */
    public function removeAll();
}
