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
 * Date
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     http://tools.ietf.org/html/rfc3339#section-5.6
 */
class Date extends \DateTime
{
    protected $year;
    protected $month;
    protected $day;

    public function __construct($date, $month = null, $day = null)
    {
        if (func_num_args() == 1) {
            parent::__construct($this->validate($date));
        } else {
            parent::__construct('@' . gmmktime(0, 0, 0, $month, $day, $date));
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

    public function toString()
    {
        $date   = $this->format('Y-m-d');
        $offset = $this->getOffset();

        if ($offset != 0) {
            $date.= DateTime::getOffsetBySeconds($offset);
        }

        return $date;
    }

    public function __toString()
    {
        return $this->toString();
    }

    protected function validate($date)
    {
        $date   = (string) $date;
        $result = preg_match('/^' . self::getPattern() . '$/', $date);

        if (!$result) {
            throw new InvalidArgumentException('Must be valid date format');
        }

        return $date;
    }

    public static function fromDateTime(\DateTime $date)
    {
        return new self($date->format('Y'), $date->format('m'), $date->format('d'));
    }

    /**
     * @see http://www.w3.org/TR/2012/REC-xmlschema11-2-20120405/datatypes.html#date-lexical-mapping
     */
    public static function getPattern()
    {
        return '-?([1-9][0-9]{3,}|0[0-9]{3})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])(Z|(\+|-)((0[0-9]|1[0-3]):([0-5][0-9]|14:00)))?';
    }
}
