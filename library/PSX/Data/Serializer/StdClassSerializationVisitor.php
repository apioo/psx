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

namespace PSX\Data\Serializer;

use JMS\Serializer\AbstractVisitor;
use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\GenericSerializationVisitor;

use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * Basically this is a copy of the GenericSerializationVisitor but it uses an 
 * stdClass for object storage instead of an associative array
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @author  Johannes M. Schmitt <schmittjoh@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class StdClassSerializationVisitor extends AbstractVisitor
{
	const NAME = 'stdClass';

    private $navigator;
    private $root;
    private $dataStack;
    private $data;

	public function getResult()
	{
		return $this->getRoot();
	}

    public function setNavigator(GraphNavigator $navigator)
    {
        $this->navigator = $navigator;
        $this->root = null;
        $this->dataStack = new \SplStack;
    }

    /**
     * @return GraphNavigator
     */
    public function getNavigator()
    {
        return $this->navigator;
    }

    public function visitNull($data, array $type, Context $context)
    {
        return null;
    }

    public function visitString($data, array $type, Context $context)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return (string) $data;
    }

    public function visitBoolean($data, array $type, Context $context)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return (boolean) $data;
    }

    public function visitInteger($data, array $type, Context $context)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return (int) $data;
    }

    public function visitDouble($data, array $type, Context $context)
    {
        if (null === $this->root) {
            $this->root = $data;
        }

        return (float) $data;
    }

    /**
     * @param array $data
     * @param array $type
     */
    public function visitArray($data, array $type, Context $context)
    {
        if (null === $this->root) {
            $this->root = array();
            $rs = &$this->root;
        } else {
            $rs = array();
        }

        foreach ($data as $k => $v) {
            $v = $this->navigator->accept($v, $this->getElementType($type), $context);

            if (null === $v && (!is_string($k) || !$context->shouldSerializeNull())) {
                continue;
            }

            $rs[$k] = $v;
        }

        return $rs;
    }

    public function startVisitingObject(ClassMetadata $metadata, $data, array $type, Context $context)
    {
        if (null === $this->root) {
            $this->root = new \stdClass();
        }

        $this->dataStack->push($this->data);
        $this->data = new \stdClass();
    }

    public function endVisitingObject(ClassMetadata $metadata, $data, array $type, Context $context)
    {
        $rs = $this->data;
        $this->data = $this->dataStack->pop();

        if ($this->root instanceof \stdClass && 0 === $this->dataStack->count()) {
            $this->root = $rs;
        }

        return $rs;
    }

    public function visitProperty(PropertyMetadata $metadata, $data, Context $context)
    {
        $v = $metadata->getValue($data);

        $v = $this->navigator->accept($v, $metadata->type, $context);
        if (null === $v && !$context->shouldSerializeNull()) {
            return;
        }

        $k = $this->namingStrategy->translateName($metadata);

        $this->data->$k = $v;
    }

    /**
     * Allows you to add additional data to the current object/root element.
     *
     * @param string $key
     * @param scalar|array $value This value must either be a regular scalar, or an array.
     *                            It must not contain any objects anymore.
     */
    public function addData($key, $value)
    {
        if (isset($this->data->$key)) {
            throw new InvalidArgumentException(sprintf('There is already data for "%s".', $key));
        }

        $this->data->$key = $value;
    }

    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param array|\ArrayObject $data the passed data must be understood by whatever encoding function is applied later.
     */
    public function setRoot($data)
    {
        $this->root = $data;
    }
}
