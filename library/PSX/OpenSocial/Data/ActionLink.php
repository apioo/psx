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

namespace PSX\OpenSocial\Data;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * ActionLink
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class ActionLink extends RecordAbstract
{
	protected $caption;
	protected $target;
	protected $httpVerb;

	public function getRecordInfo()
	{
		return new RecordInfo('actionLink', array(
			'caption'  => $this->caption,
			'target'   => $this->target,
			'httpVerb' => $this->httpVerb,
		));
	}

	/**
	 * @param string
	 */
	public function setCaption($caption)
	{
		$this->caption = $caption;
	}
	
	public function getCaption()
	{
		return $this->caption;
	}

	/**
	 * @param string
	 */
	public function setTarget($target)
	{
		$this->target = $target;
	}
	
	public function getTarget()
	{
		return $this->target;
	}

	/**
	 * @param string
	 */
	public function setHttpVerb($httpVerb)
	{
		$this->httpVerb = $httpVerb;
	}
	
	public function getHttpVerb()
	{
		return $this->httpVerb;
	}
}

