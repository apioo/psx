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

namespace PSX\Annotation;

/**
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class ParamAbstract
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var boolean
     */
    protected $required;

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @var array
     */
    protected $enumeration;

    public function __construct(array $values)
    {
        $this->name        = isset($values['name'])        ? $values['name']        : null;
        $this->type        = isset($values['type'])        ? $values['type']        : null;
        $this->description = isset($values['description']) ? $values['description'] : null;
        $this->required    = isset($values['required'])    ? $values['required']    : null;
        $this->pattern     = isset($values['pattern'])     ? $values['pattern']     : null;
        $this->enum        = isset($values['enum'])        ? $values['enum']        : null;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getRequired()
    {
        return $this->required;
    }

    public function getPattern()
    {
        return $this->pattern;
    }

    public function getEnum()
    {
        return $this->enum;
    }
}
