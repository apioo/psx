<?php
/*
 *  $Id: Organization.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\OpenSocial\Type;

use PSX\OpenSocial\TypeAbstract;

/**
 * PSX_OpenSocial_Type_Organization
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_OpenSocial
 * @version    $Revision: 480 $
 */
class Organization extends TypeAbstract
{
	public $address;
	public $department;
	public $description;
	public $endDate;
	public $field;
	public $location;
	public $name;
	public $salary;
	public $startDate;
	public $subfield;
	public $title;
	public $type;
	public $webpage;

	public function getName()
	{
		return 'organization';
	}

	public function getFields()
	{
		return array(

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

		);
	}
}

