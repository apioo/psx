<?php
/*
 *  $Id: PubSubHubBub.php 663 2012-10-07 16:45:52Z k42b3.x@googlemail.com $
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
 * This package is to help implementing an pubsubhubbub subscriber or publisher.
 * If you add new content to your feed you have to inform the hub about that.
 * You can use the method "notification" to send an content notification i.e.
 * <code>
 * $http = new PSX_Http(new PSX_Http_Handler_Curl());
 * $pshb = new PSX_PubSubHubBub($http);
 * $url  = ''; # this is the absolute url to your feed
 *
 * $pshb->notification(new PSX_Url('http://pubsubhubbub.appspot.com'), $url);
 * </code>
 *
 * To implement a subscriber you have todo the following steps. First you have
 * to subscribe to a topic (a topic is an url to an ATOM feed) you can use the
 * request method to subscribe or unsubscribe to an topic i.e.
 * <code>
 * $http  = new PSX_Http(new PSX_Http_Handler_Curl());
 * $pshb  = new PSX_PubSubHubBub($http);
 * $topic = new PSX_Url(''); # this is an url to an ATOM feed that you want subscribe
 *
 * try
 * {
 * 	# first we discover the hub
 * 	$hub = $pshb->discovery($topic);
 *
 * 	if(!empty($hub))
 * 	{
 * 		# url to the module wich extends the class psx_net_pubsubhubbub_callback
 * 		$callback = new PSX_Url('http://google.de');
 * 		$topic    = new PSX_Url('http://google.de');
 *
 * 		if($pshb->request($hub, $callback, 'subscribe', $topic, 'async'))
 * 		{
 * 			echo 'You have successful subscribe a topic'
 * 		}
 * 	}
 * 	else
 * 	{
 * 		throw new PSX_Exception('Couldnt discover hub in feed url');
 * 	}
 * }
 * catch(Exception $e)
 * {
 * 	echo 'Error: ' . $e->getMessage();
 * }
 * </code>
 *
 * More informations howto implement the callback at PubSubHubBub/CallbackAbstract.php
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_PubSubHubBub
 * @version    $Revision: 663 $
 * @see        http://code.google.com/p/pubsubhubbub/
 */
class PSX_PubSubHubBub
{
	const ATOM = 0x1;
	const RSS2 = 0x2;

	/**
	 * This contains the url of the discovered hub on null if nothing was
	 * discovered.
	 *
	 * @var string
	 */
	private $lastHub;

	private $filter;
	private $http;

	public function __construct(PSX_Http $http)
	{
		$this->http = $http;
	}

	/**
	 * Send an notification to the hub $endpoint that there is new content in
	 * the feed $topic
	 *
	 * @param string $endpoint
	 * @param string $topic
	 */
	public function notification(PSX_Url $endpoint, $topic)
	{
		$data = array(

			'hub.mode' => 'publish',
			'hub.url'  => $topic,

		);

		$header  = array(
			'User-Agent' => __CLASS__ . ' ' . PSX_Base::VERSION
		);
		$request = new PSX_Http_PostRequest($endpoint, $header, $data);

		$response  = $this->http->request($request);
		$lastError = $this->http->getLastError();

		if(empty($lastError))
		{
			if($response->getCode() >= 200 && $response->getCode() < 300)
			{
				return true;
			}
			else if($response->getCode() >= 400 && $response->getCode() < 600)
			{
				$body = $response->getBody();
				$msg  = !empty($body) ? $body : 'The hub returned an error status code';

				throw new PSX_PubSubHubBub_Exception($msg);
			}
			else
			{
				throw new PSX_PubSubHubBub_Exception('Unknown response code');
			}
		}
		else
		{
			throw new PSX_PubSubHubBub_Exception($lastError);
		}
	}

	/**
	 * Send an request to an hub either subscribe or unsubscribe. The $endpoint
	 * must be an url to an hub. If the request was successful it returns true
	 * else it throws an exception
	 *
	 * @param string $endpoint
	 * @param string $callback
	 * @param string $mode
	 * @param string $topic
	 * @param string $verify
	 * @param string $lease_seconds
	 * @param string $secret
	 * @param string $verify_token
	 * @return boolean
	 */
	public function request(PSX_Url $endpoint, PSX_Url $callback, $mode, PSX_Url $topic, $verify, $leaseSeconds = false, $secret = false, $verifyToken = false)
	{
		if(!in_array($mode, array('subscribe', 'unsubscribe')))
		{
			throw new PSX_PubSubHubBub_Exception('Invalid mode accept only "subscribe" or "unsubscribe"');
		}

		if(!in_array($verify, array('sync', 'async')))
		{
			throw new PSX_PubSubHubBub_Exception('Invalid verfication mode accept only "sync" or "async"');
		}


		$data = array(

			'hub.callback' => (string) $callback,
			'hub.mode'     => $mode,
			'hub.topic'    => (string) $topic,
			'hub.verify'   => $verify,

		);

		if(!empty($leaseSeconds))
		{
			$data['hub.lease_seconds'] = $leaseSeconds;
		}

		if(!empty($secret))
		{
			$data['hub.secret'] = $secret;
		}

		if(!empty($verifyToken))
		{
			$data['hub.verify_token'] = $verifyToken;
		}

		$request   = new PSX_Http_PostRequest($endpoint, array(), $data);

		$response  = $this->http->request($request);
		$lastError = $this->http->getLastError();

		if(empty($lastError))
		{
			if($response->getCode() >= 200 && $response->getCode() < 300)
			{
				return true;
			}
			else if($response->getCode() >= 400 && $response->getCode() < 600)
			{
				$body = $response->getBody();
				$msg  = !empty($body) ? $body : 'The hub returned an error status code';

				throw new PSX_PubSubHubBub_Exception($msg);
			}
			else
			{
				throw new PSX_PubSubHubBub_Exception('Unknown response code');
			}
		}
		else
		{
			throw new PSX_PubSubHubBub_Exception($lastError);
		}
	}

	/**
	 * $url should be an url to an ATOM or RSS feed. If the feed has an hub tag
	 * the url will be returned as PSX_Url object
	 *
	 * @param PSX_Url $url
	 * @return PSX_Url|boolean
	 */
	public function discover(PSX_Url $url, $type = 0)
	{
		$request   = new PSX_Http_GetRequest($url);
		$request->setFollowLocation(true);

		$response  = $this->http->request($request);
		$lastError = $this->http->getLastError();

		if(empty($lastError))
		{
			$reader = new PSX_Data_Reader_Dom();

			switch($type)
			{
				case self::RSS2:

					$rss = new PSX_Rss();
					$rss->import($reader->read($response));

					$elementList = $rss->getDom()->getElementsByTagNameNS(PSX_Atom::$xmlns, 'link');

					for($i = 0; $i < $elementList->length; $i++)
					{
						$link = $elementList->item($i);

						if(strcasecmp($link->getAttribute('rel'), 'hub') == 0)
						{
							$href = new PSX_Url($link->getAttribute('href'));

							return $this->lastHub = $href;
						}
					}

					break;

				case self::ATOM:
				default:

					$atom = new PSX_Atom();
					$atom->import($reader->read($response));

					foreach($atom->link as $link)
					{
						if(strcasecmp($link['rel'], 'hub') == 0)
						{
							$href = new PSX_Url($link['href']);

							return $this->lastHub = $href;
						}
					}

					break;
			}
		}
		else
		{
			throw new PSX_PubSubHubBub_Exception($lastError);
		}

		return false;
	}

	public function getLastDiscoveredHub()
	{
		return $this->lastHub;
	}
}
