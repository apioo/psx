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

namespace PSX\Framework\Dependency;

use Doctrine\Common\Annotations\Reader;
use InvalidArgumentException;
use PSX\Framework\Annotation\Inject;
use ReflectionClass;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ObjectBuilder
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ObjectBuilder implements ObjectBuilderInterface
{
    protected $container;
    protected $reader;

    public function __construct(ContainerInterface $container, Reader $reader)
    {
        $this->container = $container;
        $this->reader    = $reader;
    }

    public function getObject($className, array $constructorArguments = array(), $instanceOf = null)
    {
        $class = new ReflectionClass($className);

        if ($class->getConstructor() === null) {
            $object = $class->newInstanceArgs([]);
        } else {
            $object = $class->newInstanceArgs($constructorArguments);
        }

        if ($instanceOf !== null && !$object instanceof $instanceOf) {
            throw new InvalidArgumentException('Class ' . $className . ' must be an instanceof ' . $instanceOf);
        }

        $properties = $class->getProperties();
        foreach ($properties as $property) {
            $inject = $this->reader->getPropertyAnnotation($property, '\\PSX\\Framework\\Annotation\\Inject');
            if ($inject instanceof Inject) {
                $service = $inject->getService();
                if (empty($service)) {
                    $service = $property->getName();
                }

                if ($this->container->has($service)) {
                    $property->setAccessible(true);
                    $property->setValue($object, $this->container->get($service));
                } else {
                    throw new RuntimeException('Trying to inject a not existing service ' . $service);
                }
            }
        }

        return $object;
    }
}
