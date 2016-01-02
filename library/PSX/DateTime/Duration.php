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

use DateInterval;
use InvalidArgumentException;

/**
 * Date
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     http://tools.ietf.org/html/rfc3339#section-5.6
 */
class Duration extends DateInterval
{
    public function __construct($duration, $month = null, $day = null, $hour = null, $minute = null, $second = null)
    {
        if (func_num_args() == 1) {
            parent::__construct($this->validate($duration));
        } else {
            $interval = 'P';
            $interval.= ((int) $duration) . 'Y';
            $interval.= ((int) $month) . 'M';
            $interval.= ((int) $day) . 'D';
            $interval.= 'T';
            $interval.= ((int) $hour) . 'H';
            $interval.= ((int) $minute) . 'M';
            $interval.= ((int) $second) . 'S';

            parent::__construct($interval);
        }
    }

    public function getYear()
    {
        return $this->y;
    }

    public function getMonth()
    {
        return $this->m;
    }

    public function getDay()
    {
        return $this->d;
    }

    public function getHour()
    {
        return $this->h;
    }

    public function getMinute()
    {
        return $this->i;
    }

    public function getSecond()
    {
        return $this->s;
    }

    public function toString()
    {
        $duration = 'P';

        if ($this->y > 0) {
            $duration.= $this->y . 'Y';
        }

        if ($this->m > 0) {
            $duration.= $this->m . 'M';
        }

        if ($this->d > 0) {
            $duration.= $this->d . 'D';
        }

        if ($this->h > 0 || $this->i > 0 || $this->s > 0) {
            $duration.= 'T';

            if ($this->h > 0) {
                $duration.= $this->h . 'H';
            }

            if ($this->i > 0) {
                $duration.= $this->i . 'M';
            }

            if ($this->s > 0) {
                $duration.= $this->s . 'S';
            }
        }

        return $duration;
    }

    public function __toString()
    {
        return $this->toString();
    }

    protected function validate($duration)
    {
        $duration = (string) $duration;
        $result   = preg_match('/^' . self::getPattern() . '$/', $duration);

        if (!$result) {
            throw new InvalidArgumentException('Must be duration format');
        }

        return $duration;
    }

    public static function fromDateInterval(\DateInterval $interval)
    {
        return new self($interval->y, $interval->m, $interval->d, $interval->h, $interval->i, $interval->s);
    }

    /**
     * Returns the seconds of an DateInterval recalculating years, months etc.
     *
     * @param DateInterval $interval
     * @return integer
     */
    public static function getSecondsFromInterval(DateInterval $interval)
    {
        $map   = [31536000, 2592000, 86400, 3600, 60, 1];
        $parts = explode('.', $interval->format('%y.%m.%d.%h.%i.%s'));
        $value = 0;

        foreach ($parts as $key => $val) {
            $value+= $val * $map[$key];
        }

        return $value;
    }

    /**
     * @see http://www.w3.org/TR/2012/REC-xmlschema11-2-20120405/datatypes.html#duration-lexical-space
     */
    public static function getPattern()
    {
        return '-?P((([0-9]+Y([0-9]+M)?([0-9]+D)?|([0-9]+M)([0-9]+D)?|([0-9]+D))(T(([0-9]+H)([0-9]+M)?([0-9]+(\.[0-9]+)?S)?|([0-9]+M)([0-9]+(\.[0-9]+)?S)?|([0-9]+(\.[0-9]+)?S)))?)|(T(([0-9]+H)([0-9]+M)?([0-9]+(\.[0-9]+)?S)?|([0-9]+M)([0-9]+(\.[0-9]+)?S)?|([0-9]+(\.[0-9]+)?S))))';
    }
}
