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

namespace PSX\Dispatch\RequestFilter;

use Closure;
use PSX\Base;
use PSX\Dispatch\RequestFilterInterface;
use PSX\Exception;
use PSX\Http\Request;
use PSX\Http\Authentication;

/**
 * Implementation of the http digest authentication. Note the digest
 * authentication has the advantage that the password is not transported in
 * plaintext over the wire instead a hash is used. This has the downside that we 
 * need to rebuild and compare the hash on the server side and therefor we need 
 * the password as plain text (wich requires you to store the password as 
 * plaintext) or in exactly the hash format wich is used by the digest function 
 * "md5([username]:[realm]:[pw])" wich is probably not often the case. If you 
 * need www-authentication you probably should use https + basic authentication 
 * since you are not required to store the password in such a format
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class DigestAccessAuthentication implements RequestFilterInterface
{
	protected $ha1Callback;
	protected $successCallback;
	protected $failureCallback;
	protected $missingCallback;

	protected $nonce;
	protected $opaque;

	/**
	 * The ha1Callback should return "md5([username]:[realm]:[pw])" wich is then
	 * used to compare the response from the client. If its successful the 
	 * onSuccess callback is called else the onFailure. Of the Authorization 
	 * header is missing the onMissong callback is called. The default behavior
	 * is to store the nonce and opaque in the session but you can overwrite 
	 * that by providing a nonce and opaque in the constructor and overwrite the
	 * default onMissing callback
	 *
	 * @param Closure $ha1Callback
	 * @param string $nonce
	 * @param string $opaque
	 */
	public function __construct(Closure $ha1Callback, $nonce = null, $opaque = null)
	{
		$this->ha1Callback = $ha1Callback;

		$this->onSuccess(function(){
			// authentication successful
		});

		$this->onFailure(function(){
			throw new Exception('Invalid username or password');
		});

		$this->onMissing(function(){
			$nonce  = $_SESSION['digest_nonce']  = sha1(time() + uniqid());
			$opaque = $_SESSION['digest_opaque'] = sha1(session_id());

			$params = array(
				'realm'  => 'psx',
				'qop'    => 'auth,auth-int',
				'nonce'  => $nonce,
				'opaque' => $opaque,
			);

			Base::setResponseCode(401);
			header('WWW-Authenticate: Digest ' . Authentication::encodeParameters($params), false);

			throw new Exception('Missing authorization header');
		});

		if($nonce === null)
		{
			$this->setNonce(isset($_SESSION['digest_nonce']) ? $_SESSION['digest_nonce'] : null);
		}

		if($opaque === null)
		{
			$this->setOpaque(isset($_SESSION['digest_opaque']) ? $_SESSION['digest_opaque'] : null);
		}
	}

	public function setNonce($nonce)
	{
		$this->nonce = $nonce;
	}

	public function setOpaque($opaque)
	{
		$this->opaque = $opaque;		
	}

	public function handle(Request $request)
	{
		$authorization = $request->getHeader('Authorization');

		if(!empty($authorization))
		{
			$parts = explode(' ', $authorization, 2);
			$type  = isset($parts[0]) ? $parts[0] : null;
			$data  = isset($parts[1]) ? $parts[1] : null;

			if($type == 'Digest' && !empty($data))
			{
				$params = Authentication::decodeParameters($data);
				$algo   = isset($params['algorithm']) ? $params['algorithm'] : 'MD5';
				$qop    = isset($params['qop']) ? $params['qop'] : 'auth';

				if(empty($this->nonce))
				{
					throw new Exception('Nonce not set');
				}

				if(empty($this->opaque) || $this->opaque != $params['opaque'])
				{
					throw new Exception('Invalid opaque');
				}

				// build ha1
				$ha1 = call_user_func_array($this->ha1Callback, array($params['username']));

				if($algo == 'MD5-sess')
				{
					$ha1 = md5($ha1 . ':' . $this->nonce . ':' . $params['cnonce']);
				}

				// build ha2
				if($qop == 'auth-int')
				{
					$ha2 = md5($request->getMethod() . ':' . $request->getUrl()->getPath() . ':' . md5($request->getBody()));
				}
				else
				{
					$ha2 = md5($request->getMethod() . ':' . $request->getUrl()->getPath());
				}

				// build response
				if($qop == 'auth' || $qop == 'auth-int')
				{
					$response = md5($ha1 . ':' . $this->nonce . ':' . $params['nc'] . ':' . $params['cnonce'] . ':' . $qop . ':' . $ha2);
				}
				else
				{
					$response = md5($ha1 . ':' . $this->nonce . ':' . $ha2);
				}

				if(strcmp($response, $params['response']) === 0)
				{
					$this->callSuccess();
				}
				else
				{
					$this->callFailure();
				}
			}
			else
			{
				$this->callMissing();
			}
		}
		else
		{
			$this->callMissing();
		}
	}

	public function onSuccess(Closure $successCallback)
	{
		$this->successCallback = $successCallback;
	}

	public function onFailure(Closure $failureCallback)
	{
		$this->failureCallback = $failureCallback;
	}

	public function onMissing(Closure $missingCallback)
	{
		$this->missingCallback = $missingCallback;
	}

	protected function callSuccess()
	{
		call_user_func($this->successCallback);
	}

	protected function callFailure()
	{
		call_user_func($this->failureCallback);
	}

	protected function callMissing()
	{
		call_user_func($this->missingCallback);
	}
}
