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

namespace PSX\Swagger;

use PSX\Data\RecordAbstract;

/**
 * InfoObject
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class InfoObject extends RecordAbstract
{
	protected $title;
	protected $description;
	protected $termsOfServiceUrl;
	protected $contact;
	protected $license;
	protected $licenseUrl;

	public function __construct($title = null, $description = null)
	{
		$this->title       = $title;
		$this->description = $description;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	public function getTitle()
	{
		return $this->title;
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	public function getDescription()
	{
		return $this->description;
	}

	public function setTermsOfServiceUrl($termsOfServiceUrl)
	{
		$this->termsOfServiceUrl = $termsOfServiceUrl;
	}
	
	public function getTermsOfServiceUrl()
	{
		return $this->termsOfServiceUrl;
	}

	public function setContact($contact)
	{
		$this->contact = $contact;
	}
	
	public function getContact()
	{
		return $this->contact;
	}

	public function setLicense($license)
	{
		$this->license = $license;
	}
	
	public function getLicense()
	{
		return $this->license;
	}

	public function setLicenseUrl($licenseUrl)
	{
		$this->licenseUrl = $licenseUrl;
	}
	
	public function getLicenseUrl()
	{
		return $this->licenseUrl;
	}
}
