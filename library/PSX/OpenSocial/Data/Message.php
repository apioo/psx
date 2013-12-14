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

use DateTime;
use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Message
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Message extends RecordAbstract
{
	protected $appUrl;
	protected $body;
	protected $bodyId;
	protected $collectionIds;
	protected $id;
	protected $recipients;
	protected $senderId;
	protected $status;
	protected $timeSent;
	protected $title;
	protected $titleId;
	protected $type;
	protected $updated;
	protected $urls;

	public function getRecordInfo()
	{
		return new RecordInfo('message', array(
			'appUrl'        => $this->appUrl,
			'body'          => $this->body,
			'bodyId'        => $this->bodyId,
			'collectionIds' => $this->collectionIds,
			'id'            => $this->id,
			'recipients'    => $this->recipients,
			'senderId'      => $this->senderId,
			'status'        => $this->status,
			'timeSent'      => $this->timeSent,
			'title'         => $this->title,
			'titleId'       => $this->titleId,
			'type'          => $this->type,
			'updated'       => $this->updated,
			'urls'          => $this->urls,
		));
	}

	/**
	 * @param string
	 */
	public function setAppUrl($appUrl)
	{
		$this->appUrl = $appUrl;
	}
	
	public function getAppUrl()
	{
		return $this->appUrl;
	}

	/**
	 * @param string
	 */
	public function setBody($body)
	{
		$this->body = $body;
	}
	
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * @param string
	 */
	public function setBodyId($bodyId)
	{
		$this->bodyId = $bodyId;
	}
	
	public function getBodyId()
	{
		return $this->bodyId;
	}

	/**
	 * @param array
	 */
	public function setCollectionIds(array $collectionIds)
	{
		$this->collectionIds = $collectionIds;
	}
	
	public function getCollectionIds()
	{
		return $this->collectionIds;
	}

	/**
	 * @param string
	 */
	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param array
	 */
	public function setRecipients(array $recipients)
	{
		$this->recipients = $recipients;
	}
	
	public function getRecipients()
	{
		return $this->recipients;
	}

	/**
	 * @param string
	 */
	public function setSenderId($senderId)
	{
		$this->senderId = $senderId;
	}
	
	public function getSenderId()
	{
		return $this->senderId;
	}

	/**
	 * @param string
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}
	
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param DateTime
	 */
	public function setTimeSent(DateTime $timeSent)
	{
		$this->timeSent = $timeSent;
	}
	
	public function getTimeSent()
	{
		return $this->timeSent;
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
	public function setTitleId($titleId)
	{
		$this->titleId = $titleId;
	}
	
	public function getTitleId()
	{
		return $this->titleId;
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
	 * @param DateTime
	 */
	public function setUpdated(DateTime $updated)
	{
		$this->updated = $updated;
	}
	
	public function getUpdated()
	{
		return $this->updated;
	}

	/**
	 * @param string
	 */
	public function setUrls($urls)
	{
		$this->urls = $urls;
	}
	
	public function getUrls()
	{
		return $this->urls;
	}
}

