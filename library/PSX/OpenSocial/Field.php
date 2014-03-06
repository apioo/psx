<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\OpenSocial;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Field
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Field extends RecordAbstract
{
	protected $value;
	protected $type;
	protected $primary;

	public function getRecordInfo()
	{
		return new RecordInfo('field', array(
			'value'   => $this->value,
			'type'    => $this->type,
			'primary' => $this->primary,
		));
	}

	/**
	 * @param string
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	public function getValue()
	{
		return $this->value;
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
	 * @param boolean
	 */
	public function setPrimary($primary)
	{
		$this->primary = $primary;
	}
	
	public function getPrimary()
	{
		return $this->primary;
	}
}

