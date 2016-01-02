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

namespace PSX\Http;

/**
 * Util class to handle Authentication header
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Authentication
{
    public static function decodeParameters($data)
    {
        $params = array();
        $parts  = explode(',', $data);

        foreach ($parts as $value) {
            $value = trim($value);
            $pair  = explode('=', $value);

            $key   = isset($pair[0]) ? $pair[0] : null;
            $value = isset($pair[1]) ? $pair[1] : null;

            if (!empty($key)) {
                $key   = strtolower($key);
                $value = trim($value, '"');

                $params[$key] = $value;
            }
        }

        return $params;
    }

    public static function encodeParameters(array $params)
    {
        $parts = array();

        foreach ($params as $key => $value) {
            $parts[] = $key . '="' . $value . '"';
        }

        return implode(', ', $parts);
    }
}
