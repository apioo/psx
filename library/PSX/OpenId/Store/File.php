<?php
/*
 *  $Id: File.php 573 2012-08-11 18:43:12Z k42b3.x@googlemail.com $
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
 * PSX_OpenId_Store_File
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_OpenId
 * @version    $Revision: 573 $
 */
class PSX_OpenId_Store_File implements PSX_OpenId_StoreInterface
{
	private $file;

	public function __construct($file = null)
	{
		$this->file = PSX_PATH_CACHE . '/' . ($file === null ? strtolower(__CLASS__ . '.store') : $file);

		if(!PSX_File::exists($this->file))
		{
			PSX_File::putContents($this->file, serialize(array()));
		}
	}

	public function load($opEndpoint)
	{
		$data = unserialize(PSX_File::getContents($this->file));
		$key  = md5($opEndpoint);
		$row  = isset($data[$key]) ? $data[$key] : null;

		if(!empty($row))
		{
			$assoc = new PSX_OpenId_Provider_Data_Association();
			$assoc->setAssocHandle($row['assocHandle']);
			$assoc->setAssocType($row['assocType']);
			$assoc->setSessionType($row['sessionType']);
			$assoc->setSecret($row['secret']);
			$assoc->setExpire($row['expires']);

			return $assoc;
		}

		return null;
	}

	public function loadByHandle($opEndpoint, $assocHandle)
	{
		$data = unserialize(PSX_File::getContents($this->file));
		$key  = md5($opEndpoint);
		$row  = isset($data[$key]) ? $data[$key] : null;

		if(!empty($row) && $row['assocHandle'] == $assocHandle)
		{
			$assoc = new PSX_OpenId_Provider_Data_Association();
			$assoc->setAssocHandle($row['assocHandle']);
			$assoc->setAssocType($row['assocType']);
			$assoc->setSessionType($row['sessionType']);
			$assoc->setSecret($row['secret']);
			$assoc->setExpire($row['expires']);

			return $assoc;
		}

		return null;
	}

	public function remove($opEndpoint, $assocHandle)
	{
		$data = unserialize(PSX_File::getContents($this->file));
		$key  = md5($opEndpoint);
		$row  = isset($data[$key]) ? $data[$key] : null;

		if(!empty($row) && $row['assocHandle'] == $assocHandle)
		{
			unset($data[$key]);

			PSX_File::putContents($this->file, serialize($data));
		}
	}

	public function save($opEndpoint, PSX_OpenId_Provider_Data_Association $assoc)
	{
		$data = unserialize(PSX_File::getContents($this->file));
		$key  = md5($opEndpoint);

		$data[$key] = array(

			'opEndpoint'  => $opEndpoint,
			'assocHandle' => $assoc->getAssocHandle(),
			'assocType'   => $assoc->getAssocType(),
			'sessionType' => $assoc->getSessionType(),
			'secret'      => $assoc->getSecret(),
			'expires'     => $assoc->getExpire(),

		);

		PSX_File::putContents($this->file, serialize($data));
	}
}