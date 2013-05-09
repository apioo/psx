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

namespace PSX\ActivityStream;

use DateTime;
use PSX\Data\RecordAbstract;

/**
 * Activity
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Activity extends Object
{
	protected $actor;
	protected $bcc;
	protected $bto;
	protected $cc;
	protected $content;
	protected $context;
	protected $generator;
	protected $icon;
	protected $id;
	protected $inReplyTo;
	protected $object;
	protected $provider;
	protected $target;
	protected $title;
	protected $to;
	protected $url;
	protected $updated;
	protected $verb;

	public function getName()
	{
		return 'activity';
	}

	public function getFields()
	{
		return array_merge(parent::getFields(), array(

			'actor'     => $this->actor,
			'bcc'       => $this->bcc,
			'bto'       => $this->bto,
			'cc'        => $this->cc,
			'content'   => $this->content,
			'context'   => $this->context,
			'generator' => $this->generator,
			'icon'      => $this->icon,
			'id'        => $this->id,
			'inReplyTo' => $this->inReplyTo,
			'object'    => $this->object,
			'provider'  => $this->provider,
			'target'    => $this->target,
			'title'     => $this->title,
			'to'        => $this->to,
			'url'       => $this->url,
			'updated'   => $this->updated !== null ? $this->updated->format(DateTime::RFC3339) : null,
			'verb'      => $this->verb,

		));
	}

	/**
	 * @param PSX\ActivityStream\ObjectFactory
	 */
	public function setActor(Object $actor)
	{
		$this->actor = $actor;
	}

	/**
	 * @param array<PSX\ActivityStream\ObjectFactory>
	 */
	public function setBcc(array $bcc)
	{
		$this->bcc = $bcc;
	}

	/**
	 * @param array<PSX\ActivityStream\ObjectFactory>
	 */
	public function setBto(array $bto)
	{
		$this->bto = $bto;
	}

	/**
	 * @param array<PSX\ActivityStream\ObjectFactory>
	 */
	public function setCc(array $cc)
	{
		$this->cc = $cc;
	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	/**
	 * @param PSX\ActivityStream\Context
	 */
	public function setContext(Context $context)
	{
		$this->context = $context;
	}

	/**
	 * @param PSX\ActivityStream\Source
	 */
	public function setGenerator(Source $generator)
	{
		$this->generator = $generator;
	}

	public function setIcon($icon)
	{
		$this->icon = $icon;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function setInReplyTo($inReplyTo)
	{
		$this->inReplyTo = $inReplyTo;
	}

	/**
	 * @param PSX\ActivityStream\ObjectFactory
	 */
	public function setObject(Object $object)
	{
		$this->object = $object;
	}

	/**
	 * @param PSX\ActivityStream\Source
	 */
	public function setProvider(Source $provider)
	{
		$this->provider = $provider;
	}

	/**
	 * @param PSX\ActivityStream\ObjectFactory
	 */
	public function setTarget(Object $target)
	{
		$this->target = $target;
	}

	public function setTitle($title)
	{
		$this->title = $title;
	}

	public function setTo($to)
	{
		$this->to = $to;
	}

	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * @param DateTime
	 */
	public function setUpdated(DateTime $updated)
	{
		$this->updated = $updated;
	}

	public function setVerb($verb)
	{
		$this->verb = $verb;
	}
}

