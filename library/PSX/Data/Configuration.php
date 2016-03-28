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

namespace PSX\Data;

use Doctrine\Common\Annotations\Reader as AnnotationReader;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Configuration
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Configuration
{
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    protected $annotationReader;

    /**
     * @var \Psr\Cache\CacheItemPoolInterface
     */
    protected $cache;

    /**
     * @var boolean
     */
    protected $debug;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var \PSX\Data\ReaderFactory
     */
    protected $readerFactory;

    /**
     * @var \PSX\Data\WriterFactory
     */
    protected $writerFactory;

    public function __construct(AnnotationReader $reader, CacheItemPoolInterface $cache, $debug, $namespace)
    {
        $this->annotationReader = $reader;
        $this->cache            = $cache;
        $this->debug            = $debug;
        $this->namespace        = $namespace;
        $this->readerFactory    = new ReaderFactory();
        $this->writerFactory    = new WriterFactory();
    }

    /**
     * @return \Doctrine\Common\Annotations\Reader
     */
    public function getAnnotationReader()
    {
        return $this->annotationReader;
    }

    /**
     * @return \Psr\Cache\CacheItemPoolInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @return boolean
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return \PSX\Data\ReaderFactory
     */
    public function getReaderFactory()
    {
        return $this->readerFactory;
    }

    /**
     * @return \PSX\Data\WriterFactory
     */
    public function getWriterFactory()
    {
        return $this->writerFactory;
    }

    public static function createDefault(AnnotationReader $reader, CacheItemPoolInterface $cache, $debug = false, $namespace = null)
    {
        $soapNamespace = $namespace !== null ? $namespace : 'http://phpsx.org/2014/data';
        $configuration = new self($reader, $cache, $debug, $namespace);

        $configuration->getReaderFactory()->addReader(new Reader\Json(), 16);
        $configuration->getReaderFactory()->addReader(new Reader\Form(), 8);
        $configuration->getReaderFactory()->addReader(new Reader\Xml(), 0);

        $configuration->getWriterFactory()->addWriter(new Writer\Json(), 48);
        $configuration->getWriterFactory()->addWriter(new Writer\Atom(), 32);
        $configuration->getWriterFactory()->addWriter(new Writer\Form(), 24);
        $configuration->getWriterFactory()->addWriter(new Writer\Jsonp(), 16);
        $configuration->getWriterFactory()->addWriter(new Writer\Jsonx(), 15);
        $configuration->getWriterFactory()->addWriter(new Writer\Soap($soapNamespace), 8);
        $configuration->getWriterFactory()->addWriter(new Writer\Xml(), 0);

        return $configuration;
    }
}
