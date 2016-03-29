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

namespace PSX\Validate;

/**
 * A filter is a class which validates a value. If the filter returns true
 * the value is valid. If it returns false it is invalid. In every other case
 * the filter overwrites the value
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface FilterInterface
{
    /**
     * Applies the filter to the $value
     *
     * @param mixed $value
     * @return mixed
     */
    public function apply($value);

    /**
     * A filter can overwrite this method to provide a custom error message.
     * The error message can contain one %s which is replaced with the name
     * of the field
     *
     * @return string
     */
    public function getErrorMessage();
}
