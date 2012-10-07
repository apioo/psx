<?php
/*
 *  $Id: CallbackAbstract.php 588 2012-08-15 21:30:10Z k42b3.x@googlemail.com $
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
 * PSX_PubSubHubBub_CallbackAbstract
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_PubSubHubBub
 * @version    $Revision: 588 $
 */
abstract class PSX_PubSubHubBub_CallbackAbstract extends PSX_Module_ApiAbstract
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
		switch(PSX_Base::getRequestMethod())
		{
			case 'POST':

				$this->callback();

				PSX_Base::setResponseCode(200);
				//header('X-Hub-On-Behalf-Of: 0');

				exit;

				break;

			case 'GET':

				$this->verify();

				break;

			default:

				throw new PSX_PubSubHubBub_Exception('PubSubHubBub subscriber endpoint');

				break;
		}
	}

	protected function callback()
	{
		$contentType = PSX_Base::getRequestHeader('content-type');

		switch($contentType)
		{
			case 'application/atom+xml':

				$atom = new PSX_Atom();

				$atom->import($this->getRequest(PSX_Data_ReaderInterface::DOM));

				$this->onAtom($atom);

				break;

			case 'application/rss+xml':

				$rss = new PSX_Rss();

				$rss->import($this->getRequest(PSX_Data_ReaderInterface::DOM));

				$this->onRss($rss);

				break;

			default:

				throw new PSX_PubSubHubBub_Exception('Invalid content type allowed is only application/atom+xml or application/rss+xml');

				break;
		}
	}

	protected function verify()
	{
		$validate = new PSX_Validate();
		$get      = new PSX_Input_Get($validate);

		$mode         = $get->hub_mode('string', array(new PSX_Filter_InArray(array('subscribe', 'unsubscribe'))), 'hub.mode', 'Mode');
		$topic        = $get->hub_topic('string', array(new PSX_Filter_Length(3, 512), new PSX_Filter_Url()), 'hub.topic', 'Topic');
		$challenge    = $get->hub_challenge('string', array(new PSX_Filter_Length(1, 512), 'hub.challenge', 'Challenge'));
		$leaseSeconds = $get->hub_lease_seconds('integer', null, 'hub.lease_seconds', 'Lease seconds', false);
		$verifyToken  = $get->hub_verify_token('string', null, 'hub.verify_token', 'Verify token', false);


		if(!$validate->hasError())
		{
			$topic = new PSX_Url($topic);

			if($this->onVerify($mode, $topic, $leaseSeconds, $verifyToken) === true)
			{
				PSX_Base::setResponseCode(200);

				echo $challenge;

				exit;
			}
			else
			{
				throw new PSX_PubSubHubBub_Exception('Invalid token');
			}
		}
		else
		{
			throw new PSX_PubSubHubBub_Exception($validate->getLastError());
		}
	}

	/**
	 * Is called if the incomming entry from an hub is an atom entry
	 *
	 * @return void
	 */
	abstract protected function onAtom(PSX_Atom $atom);

	/**
	 * Is called if the incomming entry from an hub is an rss entry
	 *
	 * @return void
	 */
	abstract protected function onRss(PSX_Rss $rss);

	/**
	 * This method is called if an verify request from the hub occurs. $mode is
	 * either subscribe or unsubscribe. The method must return true or false
	 * whether the subscription was verified. If the method returns false the
	 * hub will probably try to verify the request later
	 *
	 * @return boolean
	 */
	abstract protected function onVerify($mode, PSX_Url $topic, $leaseSeconds, $verifyToken);
}

