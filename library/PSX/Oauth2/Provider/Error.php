<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Oauth2\Provider;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Error
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Error extends RecordAbstract
{
	protected $error;
	protected $errorDescription;
	protected $errorUri;
	protected $state;

	public function getRecordInfo()
	{
		return new RecordInfo('error', array(
			'error' => $this->error,
			'error_description' => $this->errorDescription,
			'error_uri' => $this->errorUri,
			'state' => $this->state,
		));
	}

	public function setError($error)
	{
		$this->error = $error;
	}
	
	public function getError()
	{
		return $this->error;
	}

	public function setErrorDescription($errorDescription)
	{
		$this->errorDescription = $errorDescription;
	}
	
	public function getErrorDescription()
	{
		return $this->errorDescription;
	}

	public function setErrorUri($errorUri)
	{
		$this->errorUri = $errorUri;
	}
	
	public function getErrorUri()
	{
		return $this->errorUri;
	}

	public function setState($state)
	{
		$this->state = $state;
	}
	
	public function getState()
	{
		return $this->state;
	}
}
