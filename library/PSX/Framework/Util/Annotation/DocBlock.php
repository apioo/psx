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

namespace PSX\Framework\Util\Annotation;

/**
 * DocBlock
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class DocBlock
{
    protected $annotations = array();

    /**
     * Adds an annotation
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function addAnnotation($key, $value)
    {
        if (!isset($this->annotations[$key])) {
            $this->annotations[$key] = array();
        }

        $this->annotations[$key][] = $value;
    }

    /**
     * Returns all annotations associated with the $key
     *
     * @param string $key
     * @return array
     */
    public function getAnnotation($key)
    {
        if (isset($this->annotations[$key])) {
            return $this->annotations[$key];
        } else {
            return array();
        }
    }

    public function hasAnnotation($key)
    {
        return isset($this->annotations[$key]);
    }

    public function removeAnnotation($key)
    {
        unset($this->annotations[$key]);
    }

    public function getAnnotations()
    {
        return $this->annotations;
    }

    public function setAnnotations($key, array $values)
    {
        $this->annotations[$key] = $values;
    }

    /**
     * Returns te first annotation for the $key
     *
     * @param string $key
     * @return string|null
     */
    public function getFirstAnnotation($key)
    {
        $annotation = $this->getAnnotation($key);

        return isset($annotation[0]) ? $annotation[0] : null;
    }
}
