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

/**
 * Util class to generate time based, pseudo random or name based UUIDs
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     http://www.ietf.org/rfc/rfc4122.txt
 */
class Uuid
{
    const V_1 = 0x1000;
    const V_2 = 0x2000;
    const V_3 = 0x3000;
    const V_4 = 0x4000;
    const V_5 = 0x5000;

    public static function timeBased()
    {
        return self::generate(self::V_1, sha1(microtime()));
    }

    public static function pseudoRandom()
    {
        return self::generate(self::V_4, sha1(uniqid(rand(), true)));
    }

    public static function nameBased($name)
    {
        return self::generate(self::V_5, sha1($name));
    }

    public static function generate($version, $hash)
    {
        $timeLow            = substr($hash, 0, 8);
        $timeMid            = substr($hash, 8, 4);
        $timeHighVersion    = dechex(hexdec(substr($hash, 12, 4)) & 0x0FFF | $version);
        $clockSeqHiReserved = dechex(hexdec(substr($hash, 16, 2)) & 077 | 0200);
        $clockSeqLow        = substr($hash, 17, 2);
        $node               = substr($hash, 18, 12);

        return $timeLow . '-' . $timeMid . '-' . $timeHighVersion . '-' . $clockSeqHiReserved . $clockSeqLow . '-' . $node;
    }
}
