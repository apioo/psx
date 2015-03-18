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
use JMS\Serializer\Exception\InvalidArgumentException;
use JMS\Serializer\GenericSerializationVisitor;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;

/**
 * SerializationVisitor
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @author  Johannes M. Schmitt <schmittjoh@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class SerializationVisitor extends GenericSerializationVisitor
{
	const NAME = 'psx';

    public function getResult()
    {
        return $this->getRoot();
    }
}
