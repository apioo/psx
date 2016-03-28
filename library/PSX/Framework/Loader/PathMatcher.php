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

/**
 * PathMatcher
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PathMatcher
{
    protected $srcPath;

    public function __construct($srcPath)
    {
        $this->srcPath = explode('/', trim($srcPath, '/'));
    }

    public function match($destPath, array &$parameters = array())
    {
        $hasWildcard = strpos($destPath, '*') !== false;
        $destPath    = explode('/', trim($destPath, '/'));

        if (count($this->srcPath) == count($destPath) || $hasWildcard) {
            foreach ($destPath as $key => $part) {
                if (isset($part[0]) && $part[0] == ':') {
                    $name = substr($part, 1);

                    $parameters[$name] = isset($this->srcPath[$key]) ? $this->srcPath[$key] : null;
                } elseif (isset($part[0]) && $part[0] == '$') {
                    $pos  = strpos($part, '<');
                    $name = substr($part, 1, $pos - 1);
                    $rexp = substr($part, $pos + 1, -1);

                    if (preg_match('/' . $rexp . '/', $this->srcPath[$key])) {
                        $parameters[$name] = isset($this->srcPath[$key]) ? $this->srcPath[$key] : null;
                    } else {
                        return false;
                    }
                } elseif (isset($part[0]) && $part[0] == '*') {
                    $name = substr($part, 1);

                    $parameters[$name] = implode('/', array_slice($this->srcPath, $key));

                    return true;
                } elseif ($this->srcPath[$key] == $part) {
                } else {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}
