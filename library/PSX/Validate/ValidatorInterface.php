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
 * ValidatorInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface ValidatorInterface
{
    const THROW_ERRORS   = 1;
    const COLLECT_ERRORS = 2;

    /**
     * Goes through the data structure and calls for each property the
     * validateProperty method. Data must be an object structure
     *
     * @param mixed $data
     * @return void
     */
    public function validate($data);

    /**
     * Validates a specific property. Searches a fitting validation property
     * inside the list of available rules and applies all filters to the data
     *
     * @param string $path
     * @param mixed $data
     * @return void
     */
    public function validateProperty($path, $data);
}
