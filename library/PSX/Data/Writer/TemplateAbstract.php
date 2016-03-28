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

namespace PSX\Data\Writer;

use PSX\Data\GraphTraverser;
use PSX\Data\Visitor;
use PSX\Data\WriterInterface;
use PSX\Framework\Loader\ReverseRouter;
use PSX\Framework\Template\GeneratorFactory;
use PSX\Framework\Template\GeneratorFactoryInterface;
use PSX\Framework\Template\GeneratorInterface;
use PSX\Framework\Template\TemplateInterface;
use PSX\Http\Exception as StatusCode;

/**
 * Abstract class to facilitate a template engine to produce the output. If no
 * template file was found we look for a generator which can produce this
 * content type
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class TemplateAbstract implements WriterInterface
{
    protected $template;
    protected $reverseRouter;
    protected $generatorFactory;
    protected $className;
    protected $baseDir;
    protected $controllerFile;

    public function __construct(TemplateInterface $template, ReverseRouter $reverseRouter, GeneratorFactoryInterface $generatorFactory = null)
    {
        $this->template         = $template;
        $this->reverseRouter    = $reverseRouter;
        $this->generatorFactory = $generatorFactory ?: new GeneratorFactory();
        $this->baseDir          = PSX_PATH_LIBRARY;
    }

    public function setBaseDir($baseDir)
    {
        $this->baseDir = $baseDir;
    }

    public function getBaseDir()
    {
        return $this->baseDir;
    }

    public function setControllerFile($controllerFile)
    {
        $this->controllerFile = $controllerFile;
    }

    public function getControllerFile()
    {
        return $this->controllerFile;
    }

    public function write($data)
    {
        $this->controllerFile = str_replace('\\', '/', $this->controllerFile);

        if (strpos($this->controllerFile, '/Application/') !== false) {
            $path = strstr($this->controllerFile, '/Application/', true) . '/Resource';
        } else {
            $path = pathinfo($this->controllerFile, PATHINFO_DIRNAME);
        }

        if (!$this->template->hasFile()) {
            // try to detect template file if we have no explicit file set
            $ext  = $this->getFileExtension();

            if (strpos($this->controllerFile, '/Application/') !== false) {
                $file = substr(strstr($this->controllerFile, 'Application'), 12);
            } else {
                $file = pathinfo($this->controllerFile, PATHINFO_BASENAME);
            }

            // remove file extension
            $pos = strrpos($file, '.');
            if ($pos !== false) {
                $file = substr($file, 0, $pos);
            }

            $file = $this->underscore($file) . '.' . $ext;

            $this->template->setDir($path);
            $this->template->set($file);
        } else {
            // if we have an absolute file we must check whether the file
            // extension is the same as the given writer needs. If not we set
            // the right extension
            if ($this->template->isAbsoluteFile()) {
                $ext  = $this->getFileExtension();
                $file = $this->template->get();
                $pos  = strrpos($file, '.');

                if ($pos !== false) {
                    if (substr($file, $pos + 1) != $ext) {
                        $this->template->set(substr($file, 0, $pos) . '.' . $ext);
                    }
                } else {
                    $this->template->set($file . '.' . $ext);
                }

                $this->template->setDir(null);
            } else {
                $this->template->setDir($path);
            }
        }

        if (!$this->template->isFileAvailable()) {
            // if we hvae no template we check whether we have a generator which
            // can generate a generic representation of the data
            $generator = $this->generatorFactory->getByContentType($this->getContentType());

            if ($generator instanceof GeneratorInterface) {
                return $generator->generate($data);
            } else {
                throw new StatusCode\UnsupportedMediaTypeException('Content is not available in the requested media type');
            }
        } else {
            // assign default values
            $self   = isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ? $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] : $_SERVER['PHP_SELF'];
            $render = round(microtime(true) - $GLOBALS['psx_benchmark'], 6);

            $this->template->assign('self', htmlspecialchars($self));
            $this->template->assign('url', $this->reverseRouter->getDispatchUrl());
            $this->template->assign('base', $this->reverseRouter->getBasePath());
            $this->template->assign('render', $render);
            $this->template->assign('location', $path);
            $this->template->assign('router', $this->reverseRouter);
            $this->template->assign('controllerClass', $this->className);

            // assign data
            $fields = $this->getNormalizedData($data);

            foreach ($fields as $key => $value) {
                $this->template->assign($key, $value);
            }

            return $this->template->transform();
        }
    }

    /**
     * Returns the file extension which is used by the template file. The file
     * extension must not include a leading dot
     *
     * @return string
     */
    abstract public function getFileExtension();

    protected function underscore($word)
    {
        return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $word));
    }

    protected function getNormalizedData($data)
    {
        $visitor = new Visitor\StdClassSerializeVisitor();
        $graph   = new GraphTraverser();
        $graph->traverse($data, $visitor);

        return $visitor->getObject();
    }
}
