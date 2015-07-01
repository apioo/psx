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

namespace PSX\Util;

use UnexpectedValueException;

/**
 * Numerative
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Numerative
{
    const BIN = 0x1;
    const OCT = 0x2;
    const DEC = 0x3;
    const HEX = 0x4;

    protected static $systems = array(
        self::BIN => array(0 => '0', 1 => '1'),
        self::OCT => array(0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7'),
        self::DEC => array(0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9'),
        self::HEX => array(0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => 'A', 11 => 'B', 12 => 'C', 13 => 'D', 14 => 'E', 15 => 'F'),
    );

    public static function addAlphabet($key, array $alphabet)
    {
        self::$systems[$key] = $alphabet;
    }

    public static function removeAlphabet($key)
    {
        if (isset(self::$systems[$key])) {
            unset(self::$systems[$key]);
        }
    }

    public static function bin2oct($bin)
    {
        return self::decToX(self::OCT, self::xToDec(self::BIN, $bin));
    }

    public static function bin2dez($bin)
    {
        return self::decToX(self::DEC, self::xToDec(self::BIN, $bin));
    }

    public static function bin2hex($bin)
    {
        return self::decToX(self::HEX, self::xToDec(self::BIN, $bin));
    }

    public static function oct2bin($oct)
    {
        return self::decToX(self::BIN, self::xToDec(self::OCT, $oct));
    }

    public static function oct2dez($oct)
    {
        return self::decToX(self::DEC, self::xToDec(self::OCT, $oct));
    }

    public static function oct2hex($oct)
    {
        return self::decToX(self::HEX, self::xToDec(self::OCT, $oct));
    }

    public static function dez2bin($dez)
    {
        return self::decToX(self::BIN, $dez);
    }

    public static function dez2oct($dez)
    {
        return self::decToX(self::OCT, $dez);
    }

    public static function dez2hex($dez)
    {
        return self::decToX(self::HEX, $dez);
    }

    public static function hex2bin($hex)
    {
        return self::decToX(self::BIN, self::xToDec(self::HEX, $hex));
    }

    public static function hex2oct($hex)
    {
        return self::decToX(self::OCT, self::xToDec(self::HEX, $hex));
    }

    public static function hex2dez($hex)
    {
        return self::decToX(self::DEC, self::xToDec(self::HEX, $hex));
    }

    public static function xToDec($system, $x)
    {
        if (!isset(self::$systems[$system])) {
            throw new UnexpectedValueException('Invalid numerative system');
        } else {
            $d = '';
            $n = array_flip(self::$systems[$system]);
            $x = strrev(strval($x));
            $c = 1;

            for ($i = 0; $i < strlen($x); $i++) {
                $d+= $n[$x{$i}] * $c;

                $c*= count($n);
            }

            return $d;
        }
    }

    public static function decToX($system, $dez)
    {
        if (!isset(self::$systems[$system])) {
            throw new UnexpectedValueException('Invalid numerative system');
        } else {
            $n = self::$systems[$system];
            $d = intval($dez);
            $b = count($n);
            $x = '';

            if ($d == 0) {
                return $n[0];
            }

            if ($d < 0) {
                throw new UnexpectedValueException('Cant convert negative numbers because we have no sign to display negative values');
            }

            while ($d > 0) {
                $x.= $n[intval($d % $b)];
                $d = intval($d / $b);
            }

            return strrev($x);
        }
    }
}
