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

use Doctrine\Common\Annotations;
use Doctrine\ORM;
use JMS\Serializer;

/**
 * Simple trait which show cases how you can include third party services
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
trait ThirdParty
{
    /**
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        $connection = $this->get('connection');
        $cache      = $this->newDoctrineCacheImpl('annotations/orm');
        $isDebug    = $this->get('config')->get('psx_debug');

        $driver = new ORM\Mapping\Driver\AnnotationDriver(
            new Annotations\CachedReader(
                new Annotations\AnnotationReader(),
                $cache,
                $isDebug
            ),
            $this->get('config')->get('psx_entity_paths')
        );

        $config = ORM\Tools\Setup::createConfiguration(
            $isDebug,
            PSX_PATH_CACHE . '/proxy',
            $cache
        );

        $config->setMetadataDriverImpl($driver);

        return ORM\EntityManager::create($connection, $config, $connection->getEventManager());
    }

    /**
     * @return \JMS\Serializer\SerializerInterface
     */
    public function getSerializer()
    {
        $reader = new Annotations\CachedReader(
            new Annotations\AnnotationReader(),
            $this->newDoctrineCacheImpl('annotations/jms'),
            $this->get('config')->get('psx_debug')
        );

        return Serializer\SerializerBuilder::create()
            ->setAnnotationReader($reader)
            ->setDebug($this->get('config')->get('psx_debug'))
            ->build();
    }
}
