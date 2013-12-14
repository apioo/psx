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

namespace PSX\PubSubHubBub;

use PSX\Atom;
use PSX\Atom\Importer as AtomImporter;
use PSX\Base;
use PSX\Data\ReaderInterface;
use PSX\Exception;
use PSX\Filter;
use PSX\Input\Get;
use PSX\Module\ApiAbstract;
use PSX\Rss;
use PSX\Rss\Importer as RssImporter;
use PSX\Url;
use PSX\Validate;

/**
 * CallbackAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class CallbackAbstract extends ApiAbstract
{
	/**
	 * This method is called by the module wich extends this class. If the
	 * method is called we handle all operations that means we look wich request
	 * method we have on an GET some hub tries to verify a subscription on an
	 * POST we get new ATOM feeds.
	 *
	 * @return void
	 */
	protected function handle()
	{
		switch(Base::getRequestMethod())
		{
			case 'POST':
				$this->callback();

				Base::setResponseCode(200);
				//header('X-Hub-On-Behalf-Of: 0');

				exit;
				break;

			case 'GET':
				$this->verify();
				break;

			default:
				throw new Exception('PubSubHubBub subscriber endpoint');
				break;
		}
	}

	protected function callback()
	{
		$contentType = Base::getRequestHeader('content-type');

		switch($contentType)
		{
			case 'application/atom+xml':
				$atom     = new Atom();
				$importer = new AtomImporter();
				$importer->import($atom, $this->getRequest(ReaderInterface::DOM));

				$this->onAtom($atom);
				break;

			case 'application/rss+xml':
				$rss      = new Rss();
				$importer = new RssImporter();
				$importer->import($rss, $this->getRequest(ReaderInterface::DOM));

				$this->onRss($rss);
				break;

			default:
				throw new Exception('Invalid content type allowed is only application/atom+xml or application/rss+xml');
				break;
		}
	}

	protected function verify()
	{
		$validate = new Validate();
		$get      = new Get($validate);

		$mode         = $get->hub_mode('string', array(new Filter\InArray(array('subscribe', 'unsubscribe'))), 'hub.mode', 'Mode');
		$topic        = $get->hub_topic('string', array(new Filter\Length(3, 512), new Filter\Url()), 'hub.topic', 'Topic');
		$challenge    = $get->hub_challenge('string', array(new Filter\Length(1, 512), 'hub.challenge', 'Challenge'));
		$leaseSeconds = $get->hub_lease_seconds('integer', null, 'hub.lease_seconds', 'Lease seconds', false);
		$verifyToken  = $get->hub_verify_token('string', null, 'hub.verify_token', 'Verify token', false);


		if(!$validate->hasError())
		{
			$topic = new Url($topic);

			if($this->onVerify($mode, $topic, $leaseSeconds, $verifyToken) === true)
			{
				Base::setResponseCode(200);

				echo $challenge;
				exit;
			}
			else
			{
				throw new Exception('Invalid token');
			}
		}
		else
		{
			throw new Exception($validate->getLastError());
		}
	}

	/**
	 * Is called if the incomming entry from an hub is an atom entry
	 *
	 * @return void
	 */
	abstract protected function onAtom(Atom $atom);

	/**
	 * Is called if the incomming entry from an hub is an rss entry
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
	 * @return boolean
	 */
	abstract protected function onVerify($mode, Url $topic, $leaseSeconds, $verifyToken);
}

