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

namespace PSX\Api\Util;

/**
 * Inflection
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Inflection
{
    /**
     * Transforms placeholder of an PSX route "/bar/:foo" into an curly bracket
     * "/bar/{foo}" route
     *
     * @param string $path
     * @return string
     */
    public static function transformRoutePlaceholder($path)
    {
        return preg_replace('/(\:|\*)(\w+)/i', '{$2}', $path);
    }

    /**
     * Generates an title "BarFoo" based on an PSX route "/bar/:foo"
     *
     * @param string $path
     * @return string
     */
    public static function generateTitleFromRoute($path)
    {
        $path = str_replace(':', '', $path);
        $path = str_replace(' ', '', ucwords(str_replace('/', ' ', $path)));

        return $path;
    }
}
