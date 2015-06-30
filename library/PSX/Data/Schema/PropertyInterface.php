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

namespace PSX\Data\Schema;

/**
 * PropertyInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
interface PropertyInterface
{
    /**
     * @param string $name
     * @return self
     */
    public function setName($name);

	/**
	 * Returns the name of the property
	 *
	 * @return string
	 */
	public function getName();

    /**
     * @param string $description
     * @return self
     */
    public function setDescription($description);

	/**
	 * Returns an description of this property
	 *
	 * @return string
	 */
	public function getDescription();

    /**
     * @param boolean $required
     * @return self
     */
    public function setRequired($required);

	/**
	 * Returns whether this property is required
	 *
	 * @return boolean
	 */
	public function isRequired();

    /**
     * @param string $reference
     * @return self
     */
    public function setReference($reference);

	/**
	 * Returns the class name which should be used to create this property
	 *
	 * @return string
	 */
	public function getReference();

	/**
	 * Returns an identifier which represents the property. Properties which 
	 * have the same identifier have the same semantic meaning
	 *
	 * @return string
	 */
	public function getId();
}
