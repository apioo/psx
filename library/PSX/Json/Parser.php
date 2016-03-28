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

namespace PSX\Json;

/**
 * This class is a wrapper to the json_encode / json_decode functions. Here an
 * simple example howto use it.
 * <code>
 * $json = Parser::encode(array('foo' => 'bar'));
 * $php  = Parser::decode($json);
 * </code>
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Parser
{
    /**
     * Returns the json encoded value as string of $value
     *
     * @param mixed $value
     * @param integer $options
     * @return string
     */
    public static function encode($value, $options = 0)
    {
        return json_encode($value, $options);
    }

    /**
     * Returns a php variable from the json decoded value. Throws an exception
     * if decoding the data is not possible
     *
     * @param string $value
     * @param boolean $assoc
     * @return mixed
     */
    public static function decode($value, $assoc = false)
    {
        $data = json_decode((string) $value, $assoc);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $data;
                break;

            case JSON_ERROR_DEPTH:
                throw new \RuntimeException('Invalid JSON structure');
                break;

            case JSON_ERROR_STATE_MISMATCH:
            case JSON_ERROR_CTRL_CHAR:
            case JSON_ERROR_SYNTAX:
            default:
                throw new \RuntimeException('Syntax error, malformed JSON');
                break;
        }
    }
}
