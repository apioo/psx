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

namespace PSX\Data;

use DateTime;
use PSX\Util\Uuid;

/**
 * Message
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Message extends RecordAbstract
{
	protected $message;
	protected $success;

	public function __construct($message, $success)
	{
		$this->message = $message;
		$this->success = (bool) $success;
	}

	public function getRecordInfo()
	{
		return new RecordInfo('message', array(
			'message' => $this->message,
			'success' => $this->success,
		));
	}

	public function getMessage()
	{
		return $this->message;
	}

	public function setMessage($message)
	{
		$this->message = $message;
	}

	public function hasSuccess()
	{
		return $this->success;
	}

	public function setSuccess($success)
	{
		$this->success = (bool) $success;
	}
}
