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

use PSX\Data\Reader\Dom;
use PSX\Http\GetRequest;
use PSX\Http\PostRequest;

/**
 * This package is to help implementing an pubsubhubbub subscriber or publisher.
 * If you add new content to your feed you have to inform the hub about that.
 * You can use the method "notification" to send an content notification i.e.
 * <code>
 * $http = new Http();
 * $pshb = new PubSubHubBub($http);
 * $url  = ''; # this is the absolute url to your feed
 *
 * $pshb->notification(new Url('http://pubsubhubbub.appspot.com'), $url);
 * </code>
 *
 * To implement a subscriber you have todo the following steps. First you have
 * to subscribe to a topic (a topic is an url to an ATOM feed) you can use the
 * request method to subscribe or unsubscribe to an topic i.e.
 * <code>
 * $http  = new Http();
 * $pshb  = new PubSubHubBub($http);
 * $topic = new Url(''); # this is an url to an ATOM feed that you want subscribe
 *
 * try
 * {
 * 	# first we discover the hub
 * 	$hub = $pshb->discovery($topic);
 *
 * 	if(!empty($hub))
 * 	{
 * 		# url to the module wich extends the module PSX\PubSubHubBub\CallbackAbstract
 * 		$callback = new Url('http://google.de');
 * 		$topic    = new Url('http://google.de');
 *
 * 		if($pshb->request($hub, $callback, 'subscribe', $topic, 'async'))
 * 		{
 * 			echo 'You have successful subscribe a topic'
 * 		}
 * 	}
 * 	else
 * 	{
 * 		throw new Exception('Couldnt discover hub in feed url');
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
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 * @see     http://code.google.com/p/pubsubhubbub/
 */
class PubSubHubBub
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

	public function __construct(Http $http)
	{
		$this->http = $http;
	}

	/**
	 * Send an notification to the hub $endpoint that there is new content in
	 * the feed $topic
	 *
	 * @param PSX\Url $endpoint
	 * @param string $topic
	 */
	public function notification(Url $endpoint, $topic)
	{
		$data = array(

			'hub.mode' => 'publish',
			'hub.url'  => $topic,

		);

		$header  = array(
			'User-Agent' => __CLASS__ . ' ' . Base::VERSION
		);
		$request = new PostRequest($endpoint, $header, $data);

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

				throw new Exception($msg);
			}
			else
			{
				throw new Exception('Unknown response code');
			}
		}
		else
		{
			throw new Exception($lastError);
		}
	}

	/**
	 * Send an request to an hub either subscribe or unsubscribe. The $endpoint
	 * must be an url to an hub. If the request was successful it returns true
	 * else it throws an exception
	 *
	 * @param PSX\Url $endpoint
	 * @param PSX\Url $callback
	 * @param string $mode
	 * @param PSX\Url $topic
	 * @param string $verify
	 * @param string $leaseSeconds
	 * @param string $secret
	 * @param string $verifyToken
	 * @return boolean
	 */
	public function request(Url $endpoint, Url $callback, $mode, Url $topic, $verify, $leaseSeconds = false, $secret = false, $verifyToken = false)
	{
		if(!in_array($mode, array('subscribe', 'unsubscribe')))
		{
			throw new Exception('Invalid mode accept only "subscribe" or "unsubscribe"');
		}

		if(!in_array($verify, array('sync', 'async')))
		{
			throw new Exception('Invalid verfication mode accept only "sync" or "async"');
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

		$request   = new PostRequest($endpoint, array(), $data);

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

				throw new Exception($msg);
			}
			else
			{
				throw new Exception('Unknown response code');
			}
		}
		else
		{
			throw new Exception($lastError);
		}
	}

	/**
	 * $url should be an url to an ATOM or RSS feed. If the feed has an hub tag
	 * the url will be returned as PSX\Url object
	 *
	 * @param PSX\Url $url
	 * @return PSX\Url|boolean
	 */
	public function discover(Url $url, $type = 0)
	{
		$request   = new GetRequest($url);
		$request->setFollowLocation(true);

		$response  = $this->http->request($request);
		$lastError = $this->http->getLastError();

		if(empty($lastError))
		{
			$reader = new Dom();

			switch($type)
			{
				case self::RSS2:
					$dom      = $reader->read($response);
					$elements = $dom->getElementsByTagNameNS(Atom::$xmlns, 'link');
					break;

				case self::ATOM:
				default:
					$dom      = $reader->read($response);
					$elements = $dom->getElementsByTagName('link');
					break;
			}

			for($i = 0; $i < $elements->length; $i++)
			{
				$link = $elements->item($i);

				if(strcasecmp($link->getAttribute('rel'), 'hub') == 0)
				{
					$href = new Url($link->getAttribute('href'));

					return $this->lastHub = $href;
				}
			}
		}
		else
		{
			throw new Exception($lastError);
		}

		return false;
	}

	public function getLastDiscoveredHub()
	{
		return $this->lastHub;
	}
}
