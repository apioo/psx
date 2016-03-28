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

namespace PSX\Data\Visitor;

use PSX\Data\Schema\Property as SchemaProperty;
use PSX\Data\VisitorInterface;
use PSX\Validate\ValidatorInterface;

/**
 * ValidationVisitor
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ValidationVisitor implements VisitorInterface
{
    protected $validator;
    protected $pathStack = array();
    protected $arrayIndex;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function visitObjectStart($name)
    {
    }

    public function visitObjectEnd()
    {
    }

    public function visitObjectValueStart($key, $value)
    {
        $this->pathStack[] = $key;
    }

    public function visitObjectValueEnd()
    {
        array_pop($this->pathStack);
    }

    public function visitArrayStart()
    {
        $this->arrayIndex = 0;
    }

    public function visitArrayEnd()
    {
    }

    public function visitArrayValueStart($value)
    {
        $this->pathStack[] = $this->arrayIndex;

        $this->arrayIndex++;
    }

    public function visitArrayValueEnd()
    {
        array_pop($this->pathStack);
    }

    public function visitValue($value)
    {
        $this->validator->validateProperty($this->getCurrentPath(), $value);
    }

    protected function getCurrentPath()
    {
        return '/' . implode('/', $this->pathStack);
    }
}
