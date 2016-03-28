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

namespace PSX\Validate\Filter;

use InvalidArgumentException;

/**
 * Checks whether the given date is in an specific range
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://amun.phpsx.org
 */
class DateRange extends DateTime
{
    protected $from;
    protected $to;

    public function __construct(\DateTime $from = null, \DateTime $to = null, $format = null)
    {
        parent::__construct($format);

        if ($from === null && $to === null) {
            throw new InvalidArgumentException('You need to specify a from or to date');
        }

        $this->from = $from;
        $this->to   = $to;
    }

    public function apply($value)
    {
        $date = parent::apply($value);
        
        if ($date instanceof \DateTime) {
            $inRange = false;

            if ($this->from !== null && $this->to !== null) {
                $inRange = $date >= $this->from && $date <= $this->to;
            } elseif ($this->from !== null && $this->to === null) {
                $inRange = $date >= $this->from;
            } elseif ($this->from === null && $this->to !== null) {
                $inRange = $date <= $this->to;
            }

            return $inRange ? $date : false;
        } else {
            return false;
        }
    }

    public function getErrorMessage()
    {
        if ($this->from !== null && $this->to !== null) {
            return '%s is not between ' . $this->from->format('Y-m-d H:i:s') . ' and ' . $this->to->format('Y-m-d H:i:s');
        } elseif ($this->from !== null && $this->to === null) {
            return '%s is not greater or equal ' . $this->from->format('Y-m-d H:i:s');
        } elseif ($this->from === null && $this->to !== null) {
            return '%s is not lower or equal ' . $this->to->format('Y-m-d H:i:s');
        }
    }
}
