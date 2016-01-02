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

namespace PSX\Cache\Handler;

use PSX\Cache\CacheItemInterface;
use PSX\Cache\HandlerInterface;
use PSX\Cache\Item;

/**
 * Cache handle which writes cache items to an file. Note this handler does not
 * work after 2038 since the expire timestamp of the file is stored in the first
 * 32bits of the file
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class File implements HandlerInterface
{
    protected $path;

    public function __construct($path = null)
    {
        $this->path = $path === null ? PSX_PATH_CACHE : $path;
    }

    public function load($key)
    {
        $file = $this->getFile($key);

        if (is_file($file)) {
            $handle = fopen($file, 'r');
            $ttl    = unpack('I*', fread($handle, 4));
            $ttl    = (int) current($ttl);

            if ($ttl >= time()) {
                $value = stream_get_contents($handle);

                fclose($handle);

                return new Item($key, unserialize($value), true, new \DateTime('@' . $ttl));
            } else {
                fclose($handle);
            }
        }

        return new Item($key, null, false);
    }

    public function write(CacheItemInterface $item)
    {
        $file = $this->getFile($item->getKey());

        if ($item->hasExpiration()) {
            $ttl = $item->getExpiration()->getTimestamp();
        } else {
            $ttl = PHP_INT_MAX;
        }

        // we write the expire date in the first bits of the file and then the
        // content
        $data = pack('I*', $ttl);
        $data.= serialize($item->get());

        file_put_contents($file, $data);
    }

    public function remove($key)
    {
        $file = $this->getFile($key);

        if (is_file($file)) {
            unlink($file);
        }
    }

    public function removeAll()
    {
        $files = scandir($this->path);

        foreach ($files as $file) {
            $item = $this->path . '/' . $file;

            if (is_file($item) && preg_match('/^psx_(.*).cache$/', $file)) {
                unlink($item);
            }
        }

        return true;
    }

    public function getFile($key)
    {
        return $this->path . '/psx_' . $key . '.cache';
    }
}
