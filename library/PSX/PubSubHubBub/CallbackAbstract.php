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

namespace PSX\PubSubHubBub;

use PSX\Atom;
use PSX\Base;
use PSX\ControllerAbstract;
use PSX\Data\ReaderInterface;
use PSX\Exception;
use PSX\Filter;
use PSX\Rss;
use PSX\Url;
use PSX\Validate;

/**
 * CallbackAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class CallbackAbstract extends ControllerAbstract
{
	public function onGet()
	{
		$this->doVerify();
	}

	public function onPost()
	{
		$this->doCallback();
	}

	protected function doCallback()
	{
		$contentType = (string) $this->request->getHeader('Content-Type');

		switch($contentType)
		{
			case 'application/atom+xml':
				$atom = $this->importer->import(new Atom(), $this->request);

				$this->onAtom($atom);
				break;

			case 'application/rss+xml':
				$rss = $this->importer->import(new Rss(), $this->request);

				$this->onRss($rss);
				break;

			default:
				throw new Exception('Invalid content type allowed is only application/atom+xml or application/rss+xml');
				break;
		}

		$this->response->setStatusCode(200);
	}

	protected function doVerify()
	{
		$mode         = $this->getParameter('hub_mode', Validate::TYPE_STRING, array(new Filter\InArray(array('subscribe', 'unsubscribe'))), 'Mode');
		$topic        = $this->getParameter('hub_topic', Validate::TYPE_STRING, array(new Filter\Length(3, 512), new Filter\Url()), 'Topic');
		$challenge    = $this->getParameter('hub_challenge', Validate::TYPE_STRING, array(new Filter\Length(1, 512)), 'Challenge');
		$leaseSeconds = $this->getParameter('hub_lease_seconds', Validate::TYPE_INTEGER, array(), 'Lease seconds', false);

		$topic = new Url($topic);

		if($this->onVerify($mode, $topic, $leaseSeconds) === true)
		{
			$this->response->setStatusCode(200);

			$this->setBody($challenge);
		}
		else
		{
			throw new Exception('Invalid request');
		}
	}

	/**
	 * Is called if the incomming entry from an hub is an atom entry
	 *
	 * @return void
	 */
	abstract protected function onAtom(Atom $atom);

	/**
	 * Is called if the incomming entry from an hub is an rss item
	 *
	 * @return void
	 */
	abstract protected function onRss(Rss $rss);

	/**
	 * This method is called if an verify request from the hub occurs. $mode is
	 * either subscribe or unsubscribe. The method must return true or false
	 * whether the subscription was verified. If the method returns false the
	 * hub will probably try to verify the request later
	 *
	 * @param string $mode
	 * @param PSX\Url $topic
	 * @param integer $leaseSeconds
	 * @return boolean
	 */
	abstract protected function onVerify($mode, Url $topic, $leaseSeconds);
}

