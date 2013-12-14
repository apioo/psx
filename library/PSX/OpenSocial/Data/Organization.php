<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\OpenSocial\Data;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Organization
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Organization extends RecordAbstract
{
	protected $address;
	protected $department;
	protected $description;
	protected $endDate;
	protected $field;
	protected $location;
	protected $name;
	protected $salary;
	protected $startDate;
	protected $subfield;
	protected $title;
	protected $type;
	protected $webpage;

	public function getRecordInfo()
	{
		return new RecordInfo('organization', array(
			'address'     => $this->address,
			'department'  => $this->department,
			'description' => $this->description,
			'endDate'     => $this->endDate,
			'field'       => $this->field,
			'location'    => $this->location,
			'name'        => $this->name,
			'salary'      => $this->salary,
			'startDate'   => $this->startDate,
			'subfield'    => $this->subfield,
			'title'       => $this->title,
			'type'        => $this->type,
			'webpage'     => $this->webpage,
		));
	}

	/**
	 * @param PSX\OpenSocial\Data\Address
	 */
	public function setAddress(Address $address)
	{
		$this->address = $address;
	}
	
	public function getAddress()
	{
		return $this->address;
	}

	/**
	 * @param string
	 */
	public function setDepartment($department)
	{
		$this->department = $department;
	}
	
	public function getDepartment()
	{
		return $this->department;
	}

	/**
	 * @param string
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}
	
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param string
	 */
	public function setEndDate($endDate)
	{
		$this->endDate = $endDate;
	}
	
	public function getEndDate()
	{
		return $this->endDate;
	}

	/**
	 * @param string
	 */
	public function setField($field)
	{
		$this->field = $field;
	}
	
	public function getField()
	{
		return $this->field;
	}

	/**
	 * @param string
	 */
	public function setLocation($location)
	{
		$this->location = $location;
	}
	
	public function getLocation()
	{
		return $this->location;
	}

	/**
	 * @param string
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	public function getOrganizationName()
	{
		return $this->name;
	}

	/**
	 * @param string
	 */
	public function setSalary($salary)
	{
		$this->salary = $salary;
	}
	
	public function getSalary()
	{
		return $this->salary;
	}

	/**
	 * @param string
	 */
	public function setStartDate($startDate)
	{
		$this->startDate = $startDate;
	}
	
	public function getStartDate()
	{
		return $this->startDate;
	}

	/**
	 * @param string
	 */
	public function setSubfield($subfield)
	{
		$this->subfield = $subfield;
	}
	
	public function getSubfield()
	{
		return $this->subfield;
	}

	/**
	 * @param string
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param string
	 */
	public function setType($type)
	{
		$this->type = $type;
	}
	
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param string
	 */
	public function setWebpage($webpage)
	{
		$this->webpage = $webpage;
	}
	
	public function getWebpage()
	{
		return $this->webpage;
	}
}

