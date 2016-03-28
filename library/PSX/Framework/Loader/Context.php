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
 * Contains context values which are gathered around an controller
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Context
{
    /**
     * This key holds the route which was used to resolve the controller
     */
    const KEY_PATH = 'psx.path';

    /**
     * This key holds the variable fragment values from the uri path. I.e. if
     * we have an path /foo/:bar the array would look like ['bar' => 'test']
     * where test is the value from the actual request uri
     */
    const KEY_FRAGMENT = 'psx.fragment';

    /**
     * This key contains the raw source like defined in the routing file i.e.
     * Foo\Bar::doIndex
     */
    const KEY_SOURCE = 'psx.source';

    /**
     * This key contains the class name from the source
     */
    const KEY_CLASS = 'psx.class';

    /**
     * This key contains the method name from the source. This method gets then
     * executed on the controller
     */
    const KEY_METHOD = 'psx.method';

    /**
     * This key contains an array with all supported writers from an controller.
     * Gets used so that every controller has the same supported writers as the
     * origin controller
     */
    const KEY_SUPPORTED_WRITER = 'psx.supported_writer';

    /**
     * This key contains an Exception if the error controller gets invoked
     */
    const KEY_EXCEPTION = 'psx.exception';

    /**
     * @var array
     */
    protected $attributes = array();

    public function set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function has($key)
    {
        return isset($this->attributes[$key]);
    }

    public function get($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }
}
