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

namespace PSX\Framework\Util;

use PSX\Framework\Util\Annotation\DocBlock;

/**
 * Util class to parse annotations
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Annotation
{
    /**
     * Parses the annotations from the given doc block
     *
     * @param string $doc
     * @return \PSX\Framework\Util\Annotation\DocBlock
     */
    public static function parse($doc)
    {
        $block = new DocBlock();
        $lines = explode("\n", $doc);

        // remove first line
        unset($lines[0]);

        foreach ($lines as $line) {
            $line = trim($line);
            $line = substr($line, 2);

            if (isset($line[0]) && $line[0] == '@') {
                $line = substr($line, 1);
                $sp   = strpos($line, ' ');
                $bp   = strpos($line, '(');

                if ($sp !== false || $bp !== false) {
                    if ($sp !== false && $bp === false) {
                        $pos = $sp;
                    } elseif ($sp === false && $bp !== false) {
                        $pos = $bp;
                    } else {
                        $pos = $sp < $bp ? $sp : $bp;
                    }

                    $key   = substr($line, 0, $pos);
                    $value = substr($line, $pos);
                } else {
                    $key   = $line;
                    $value = null;
                }

                $key   = trim($key);
                $value = trim($value);

                if (!empty($key)) {
                    // if key contains backslashes its a namespace use only the
                    // short name
                    $pos = strrpos($key, '\\');
                    if ($pos !== false) {
                        $key = substr($key, $pos + 1);
                    }

                    $block->addAnnotation($key, $value);
                }
            }
        }

        return $block;
    }

    /**
     * Parses the constructor values from an doctrine annotation
     *
     * @param string $values
     * @return array
     */
    public static function parseAttributes($values)
    {
        $result = array();
        $values = trim($values, " \t\n\r\0\x0B()");
        $parts  = explode(',', $values);

        foreach ($parts as $part) {
            $kv    = explode('=', $part, 2);
            $key   = trim($kv[0]);
            $value = isset($kv[1]) ? $kv[1] : '';
            $value = trim($value, " \t\n\r\0\x0B\"");

            if (!empty($key)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
