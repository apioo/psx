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

namespace PSX\Data;

use PSX\Schema\RevealerInterface;
use PSX\Validate\ValidatorInterface;

/**
 * Payload
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Payload
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var string
     */
    protected $contentType;

    /**
     * @var \PSX\Data\TransformerInterface
     */
    protected $transformer;

    /**
     * Absolute class name of a specific reader or writer
     *
     * @var string
     */
    protected $rwType;

    /**
     * Array which contains absolute reader or writer class names to indicate
     * which are supported. By default all available reader or writer
     * implementations are used
     *
     * @var array
     */
    protected $rwSupported;

    /**
     * @var \PSX\Validate\ValidatorInterface
     */
    protected $validator;

    /**
     * @var \PSX\Schema\RevealerInterface
     */
    protected $revealer;

    public function __construct($data, $contentType)
    {
        $this->data        = $data;
        $this->contentType = $contentType;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType ?: 'application/json';
    }

    /**
     * @return \PSX\Data\TransformerInterface
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * @param \PSX\Data\TransformerInterface $transformer
     */
    public function setTransformer($transformer)
    {
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * @return string
     */
    public function getRwType()
    {
        return $this->rwType;
    }

    /**
     * @param string $rwType
     */
    public function setRwType($rwType)
    {
        $this->rwType = $rwType;

        return $this;
    }

    /**
     * @return array
     */
    public function getRwSupported()
    {
        return $this->rwSupported;
    }

    /**
     * @param array $rwSupported
     */
    public function setRwSupported(array $rwSupported)
    {
        $this->rwSupported = $rwSupported;

        return $this;
    }

    /**
     * @return \PSX\Validate\ValidatorInterface
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param \PSX\Validate\ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * @return \PSX\Schema\RevealerInterface
     */
    public function getRevealer()
    {
        return $this->revealer;
    }

    /**
     * @param \PSX\Schema\RevealerInterface $revealer
     */
    public function setRevealer(RevealerInterface $revealer)
    {
        $this->revealer = $revealer;

        return $this;
    }

    public static function create($data, $contentType)
    {
        return new self($data, $contentType);
    }

    public static function json($data)
    {
        return self::create($data, "application/json");
    }

    public static function xml($data)
    {
        return self::create($data, "application/xml");
    }

    public static function form($data)
    {
        return self::create($data, "application/x-www-form-urlencoded");
    }
}
