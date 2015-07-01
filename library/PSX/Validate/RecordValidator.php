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

namespace PSX\Validate;

use InvalidArgumentException;
use PSX\Data\RecordInterface;

/**
 * RecordValidator
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RecordValidator extends ValidatorAbstract
{
    public function validate($record)
    {
        if (!$record instanceof RecordInterface) {
            throw new InvalidArgumentException('Data must be an RecordInterface');
        }

        $data = $record->getRecordInfo()->getData();

        foreach ($data as $key => $value) {
            $value  = $this->getPropertyValue($this->getProperty($key), $value, $key);
            $method = 'set' . ucfirst($key);

            if (method_exists($record, $method)) {
                $record->$method($value);
            }
        }

        return $record;
    }
}
