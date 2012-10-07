<?php
/*
 *  $Id: WriterResult.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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
 * PSX_Data_WriterResult
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Data
 * @version    $Revision: 480 $
 */
class PSX_Data_WriterResult
{
	private $type;
	private $writer;
	private $params;

	public function __construct($type, PSX_Data_WriterInterface $writer)
	{
		$this->setType($type);
		$this->setWriter($writer);
	}

	public function setType($type)
	{
		$this->type = (integer) $type;
	}

	public function setWriter(PSX_Data_WriterInterface $writer)
	{
		$this->writer = $writer;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getWriter()
	{
		return $this->writer;
	}

	public function addParam($key, $param)
	{
		$this->params[$key] = $param;
	}

	public function getParam($key)
	{
		return isset($this->params[$key]) ? $this->params[$key] : null;
	}
}

