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

namespace PSX\Framework\Template;

/**
 * Interface which describes a template engine. A template engine uses a 
 * template file to transform the provided data into the response
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface TemplateInterface
{
    /**
     * Sets the dir from where to load the template file
     *
     * @param string $dir
     */
    public function setDir($dir);

    /**
     * Returns the dir
     *
     * @return string
     */
    public function getDir();

    /**
     * Sets the current template file
     *
     * @param string $file
     * @return void
     */
    public function set($file);

    /**
     * Returns the template file wich was set
     *
     * @return string
     */
    public function get();

    /**
     * Returns whether an template file was set or not
     *
     * @return boolean
     */
    public function hasFile();

    /**
     * Returns the path of the template dir and file
     *
     * @return string
     */
    public function getFile();

    /**
     * Returns true if the template engine can resolve an template file with the
     * given dir and file parameters
     *
     * @return boolean
     */
    public function isFileAvailable();

    /**
     * Returns true if the given file is an absolute file path
     *
     * @return boolean
     */
    public function isAbsoluteFile();

    /**
     * Assigns an variable to the template
     *
     * @param string $key
     * @param mixed $value
     */
    public function assign($key, $value);

    /**
     * Transforms the template file
     *
     * @return string
     */
    public function transform();
}
