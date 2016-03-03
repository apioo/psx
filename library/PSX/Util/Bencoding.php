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

namespace PSX\Util;

use InvalidArgumentException;

/**
 * Bencoding
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Bencoding
{
    public static function encode($value)
    {
        $out = '';

        switch (true) {
            case is_int($value):
                $out.= 'i' . $value . 'e';
                break;

            case is_string($value):
                $out.= strlen($value) . ':' . $value;
                break;

            case is_array($value):
                if (!CurveArray::isAssoc($value)) {
                    $out.= 'l';

                    foreach ($value as $entry) {
                        $out.= self::encode($entry);
                    }

                    $out.= 'e';
                } else {
                    $out.= 'd';

                    foreach ($value as $key => $entry) {
                        $out.= self::encode($key) . self::encode($entry);
                    }

                    $out.= 'e';
                }
                break;

            default:
                throw new InvalidArgumentException('Type must be integer / string or array');
                break;
        }

        return $out;
    }

    public static function decode($value)
    {
        list($v, $r) = self::recDecode($value);

        return $v;
    }

    private static function recDecode($value)
    {
        switch ($value[0]) {
            # list
            case 'l':
                $value = substr($value, 1, -1);
                $out   = array();

                while (!empty($value)) {
                    list($v, $r) = self::recDecode($value);

                    $value = $r;

                    if (!empty($v)) {
                        $out[] = $v;
                    }
                }

                return array($out, false);
                break;

            # dictonary
            case 'd':
                $value = substr($value, 1, -1);
                $out   = array();

                while (!empty($value)) {
                    list($k, $r) = self::recDecode($value);

                    $value = $r;

                    list($v, $r) = self::recDecode($value);

                    $value = $r;

                    if (!empty($k) && !empty($v)) {
                        $out[$k] = $v;
                    }
                }

                return array($out, false);
                break;

            # integer
            case 'i':
                return self::decodeInt($value);
                break;

            # string
            case '0':
            case '1':
            case '2':
            case '3':
            case '4':
            case '5':
            case '6':
            case '7':
            case '8':
            case '9':
                return self::decodeStr($value);
                break;

            default:
                return array(false, false);
                break;
        }
    }

    private static function decodeInt($value)
    {
        if (isset($value[0]) && $value[0] == 'i') {
            $i      = 1;
            $length = '';

            while ($value[$i] != 'e') {
                $length.= $value[$i];

                $i++;
            }

            $result = intval($length);
            $value  = substr($value, strlen($length) + 2);

            return array($result, $value);
        }

        return array(false, false);
    }

    private static function decodeStr($value)
    {
        if (is_numeric($value[0])) {
            $i      = 0;
            $length = '';

            while ($value[$i] != ':') {
                $length.= $value[$i];

                $i++;
            }

            $length = intval($length);
            $result = substr($value, $i + 1, $length);
            $value  = substr($value, strlen($length) + 1 + $length);

            return array($result, $value);
        }

        return array(false, false);
    }
}
