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

namespace PSX\DateTime;

use InvalidArgumentException;

/**
 * Stricter date time implementation which accepts only RFC3339 date time
 * strings. Note if we are dropping support for PHP 5.4 this class will extend
 * DateTimeImmutable
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     http://tools.ietf.org/html/rfc3339#section-5.6
 */
class DateTime extends \DateTime
{
    const HTTP = 'D, d M Y H:i:s \G\M\T';
    const SQL  = 'Y-m-d H:i:s';

    public function __construct($date = null, $month = null, $day = null, $hour = null, $minute = null, $second = null)
    {
        if (func_num_args() <= 1) {
            parent::__construct($this->validate($date));
        } else {
            parent::__construct('@' . gmmktime($hour, $minute, $second, $month, $day, $date));
        }
    }

    public function getYear()
    {
        return (int) $this->format('Y');
    }

    public function getMonth()
    {
        return (int) $this->format('m');
    }

    public function getDay()
    {
        return (int) $this->format('d');
    }

    public function getHour()
    {
        return (int) $this->format('H');
    }

    public function getMinute()
    {
        return (int) $this->format('i');
    }

    public function getSecond()
    {
        return (int) $this->format('s');
    }

    public function getMicroSecond()
    {
        return (int) $this->format('u');
    }

    public function toString()
    {
        $date   = $this->format('Y-m-d\TH:i:s');
        $ms     = $this->getMicroSecond();
        $offset = $this->getOffset();

        if ($ms > 0) {
            $date.= '.' . $ms;
        }

        if ($offset != 0) {
            $date.= self::getOffsetBySeconds($offset);
        } else {
            $date.= 'Z';
        }

        return $date;
    }

    public function __toString()
    {
        return $this->toString();
    }

    protected function validate($date)
    {
        if ($date === null) {
            return null;
        }

        $date = (string) $date;

        // fix so that we understand mysql date time formats
        if (isset($date[10]) && $date[10] == ' ') {
            $date[10] = 'T';
        }

        $result = preg_match('/^' . self::getPattern() . '$/', $date);

        if (!$result) {
            throw new InvalidArgumentException('Must be valid date time format');
        }

        return $date;
    }

    public static function fromDateTime(\DateTime $date)
    {
        return new self($date->format(\DateTime::RFC3339));
    }

    public static function getFormat(\DateTime $date)
    {
        if ($date instanceof Time || $date instanceof Date || $date instanceof DateTime) {
            return $date->toString();
        } else {
            return $date->getOffset() == 0 ? $date->format('Y-m-d\TH:i:s') . 'Z' : $date->format(\DateTime::RFC3339);
        }
    }

    /**
     * Returns the offset string based on the given seconds
     *
     * @param integer $seconds
     * @return string
     */
    public static function getOffsetBySeconds($seconds)
    {
        $tmp    = abs($seconds);
        $hour   = (int) ($tmp / 3600);
        $minute = (int) (($tmp % 3600) / 60);

        $result = $seconds < 0 ? '-' : '+';
        $result.= ($hour < 10  ? '0' . $hour   : $hour) . ':';
        $result.= $minute < 10 ? '0' . $minute : $minute;

        return $result;
    }

    /**
     * Returns the number of seconds from the given offset values
     *
     * @param string $sign
     * @param integer $hours
     * @param integer $minutes
     * @return integer
     */
    public static function getSecondsFromOffset($sign, $hours, $minutes)
    {
        $hours   = (int) $hours;
        $minutes = (int) $minutes;

        $offset = $hours * 3600;
        $offset+= $minutes * 60;

        if ($sign == '-') {
            $offset = $offset * -1;
        }

        return $offset;
    }

    /**
     * @see http://www.w3.org/TR/2012/REC-xmlschema11-2-20120405/datatypes.html#dateTime-lexical-mapping
     */
    public static function getPattern()
    {
        return '-?([1-9][0-9]{3,}|0[0-9]{3})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])T(([01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9](\.[0-9]+)?|(24:00:00(\.0+)?))(Z|(\+|-)((0[0-9]|1[0-3]):[0-5][0-9]|14:00))?';
    }
}
