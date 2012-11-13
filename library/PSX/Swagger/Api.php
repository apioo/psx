<?php
/*
 *  $Id: Condition.php 582 2012-08-15 21:27:02Z k42b3.x@googlemail.com $
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

/**
 * PSX_Swagger_Api
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Swagger
 * @version    $Revision: 582 $
 */
class PSX_Swagger_Api extends PSX_Data_RecordAbstract
{
	private $path;
	private $description;

	private $operations = array();

	public function __construct($path, $description)
	{
		$this->path        = $path;
		$this->description = $description;
	}

	public function addOperation(PSX_Swagger_Operation $operation)
	{
		$this->operations[] = $operation;
	}

	public function getName()
	{
		return 'api';
	}

	public function getFields()
	{
		return array(
			'path'        => $this->path,
			'description' => $this->description,
			'operations'  => $this->operations,
		);
	}
}
