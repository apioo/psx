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

namespace PSX\Json;

use PSX\Data\RecordInterface;

/**
 * Comparator
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Comparator
{
    /**
     * Compares whether two values are equals. Uses the comparsion rules
     * described in the JSON patch RFC
     *
     * @see https://tools.ietf.org/html/rfc6902#section-4.6
     * @param mixed $left
     * @param mixed $right
     * @return boolean
     */
    public static function compare($left, $right)
    {
        if (is_array($left)) {
            if (is_array($right) && count($left) === count($right)) {
                foreach ($left as $key => $value) {
                    if (isset($right[$key])) {
                        if (!self::compare($value, $right[$key])) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }

                return true;
            } else {
                return false;
            }
        } elseif ($left instanceof \stdClass) {
            if ($right instanceof \stdClass && count((array) $left) === count((array) $right)) {
                foreach ($left as $key => $value) {
                    if (isset($right->$key)) {
                        if (!self::compare($value, $right->$key)) {
                            return false;
                        }
                    } else {
                        return false;
                    }
                }

                return true;
            } else {
                return false;
            }
        } elseif ($left instanceof RecordInterface) {
            if ($right instanceof RecordInterface) {
                $leftFields  = $left->getRecordInfo()->getFields();
                $rightFields = $right->getRecordInfo()->getFields();

                if (count($leftFields) === count($rightFields)) {
                    foreach ($leftFields as $key => $value) {
                        if (isset($rightFields[$key])) {
                            if (!self::compare($value, $rightFields[$key])) {
                                return false;
                            }
                        } else {
                            return false;
                        }
                    }
                }

                return true;
            } else {
                return false;
            }
        } else {
            return $left === $right;
        }
    }
}
