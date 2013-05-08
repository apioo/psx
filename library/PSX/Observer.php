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

namespace PSX;

use PSX\Observer\ListenerInterface;

/**
 * Observer
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Observer
{
	private static $instance;

	protected $subscriber;

	private function __construct()
	{
		$this->subscriber = array();
	}

	public function subscribe($eventName, ListenerInterface $listener)
	{
		$key = spl_object_hash($listener);

		if(!isset($this->subscriber[$key]))
		{
			$this->subscriber[$key] = array(

				'listener' => $listener,
				'events'   => array(),

			);
		}

		if(!in_array($eventName, $this->subscriber[$key]['events']))
		{
			$this->subscriber[$key]['events'][] = $eventName;
		}
	}

	public function publish($eventName, $args)
	{
		foreach($this->subscriber as $key => $subscriber)
		{
			if(in_array($eventName, $subscriber['events']))
			{
				$subscriber['listener']->notify($args);
			}
		}
	}

	public static function getInstance()
	{
		if(self::$instance === null)
		{
			self::$instance = new self();
		}

		return self::$instance;
	}
}

