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

namespace PSX\OpenId\Connect;

use PSX\Data\RecordAbstract;

/**
 * BasicClient
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Parameters extends RecordAbstract
{
	protected $responseType;
	protected $clientId;
	protected $scope;
	protected $redirectUri;
	protected $state;
	protected $nonce;
	protected $display;
	protected $prompt;
	protected $maxAge;
	protected $uiLocales;
	protected $claimsLocales;
	protected $idTokenHint;
	protected $loginHint;
	protected $acrValues;

	public function getName()
	{
		return 'parameters';
	}

	public function getFields()
	{
		return array(
			'response_type' => $this->responseType,
			'client_id'     => $this->clientId,
			'scope'         => $this->scope,
			'redirect_uri'  => $this->redirectUri,
			'state'         => $this->state,
			'nonce'         => $this->nonce,
			'display'       => $this->display,
			'prompt'        => $this->prompt,
			'maxAge'        => $this->maxAge,
			'uiLocales'     => $this->uiLocales,
			'claimsLocales' => $this->claimsLocales,
			'idTokenHint'   => $this->idTokenHint,
			'loginHint'     => $this->loginHint,
			'acrValues'     => $this->acrValues,
		);
	}

	/**
	 * @param string $responseType
	 */
	public function setResponseType($responseType)
	{
		$this->responseType = $responseType;
	}
	
	public function getResponseType()
	{
		return $this->responseType;
	}

	/**
	 * @param string $clientId
	 */
	public function setClientId($clientId)
	{
		$this->clientId = $clientId;
	}
	
	public function getClientId()
	{
		return $this->clientId;
	}

	/**
	 * @param string $scope
	 */
	public function setScope($scope)
	{
		$this->scope = $scope;
	}
	
	public function getScope()
	{
		return $this->scope;
	}

	/**
	 * @param string $redirectUri
	 */
	public function setRedirectUri($redirectUri)
	{
		$this->redirectUri = $redirectUri;
	}
	
	public function getRedirectUri()
	{
		return $this->redirectUri;
	}

	/**
	 * @param string $state
	 */
	public function setState($state)
	{
		$this->state = $state;
	}
	
	public function getState()
	{
		return $this->state;
	}

	/**
	 * @param string $nonce
	 */
	public function setNonce($nonce)
	{
		$this->nonce = $nonce;
	}
	
	public function getNonce()
	{
		return $this->nonce;
	}
	
	/**
	 * @param string $display
	 */
	public function setDisplay($display)
	{
		if(!in_array($display, array('page', 'popup', 'touch', 'wap')))
		{
			throw new Exception('Invalid display value');
		}

		$this->display = $display;
	}

	public function getDisplay()
	{
		return $this->display;
	}

	/**
	 * @param string $prompt
	 */
	public function setPrompt($prompt)
	{
		if(!in_array($prompt, array('none', 'login', 'consent', 'select_account')))
		{
			throw new Exception('Invalid prompt value');
		}

		$this->prompt = $prompt;
	}
	
	public function getPrompt()
	{
		return $this->prompt;
	}

	/**
	 * @param integer $prompt
	 */
	public function setMaxAge($maxAge)
	{
		$this->maxAge = $maxAge;
	}
	
	public function getMaxAge()
	{
		return $this->maxAge;
	}

	/**
	 * @param string $uiLocales
	 */
	public function setUiLocales($uiLocales)
	{
		$this->uiLocales = $uiLocales;
	}
	
	public function getUiLocales()
	{
		return $this->uiLocales;
	}

	/**
	 * @param string $claimsLocales
	 */
	public function setClaimsLocales($claimsLocales)
	{
		$this->claimsLocales = $claimsLocales;
	}
	
	public function getClaimsLocales()
	{
		return $this->claimsLocales;
	}

	/**
	 * @param string $idTokenHint
	 */
	public function setIdTokenHint($idTokenHint)
	{
		$this->idTokenHint = $idTokenHint;
	}
	
	public function getIdTokenHint()
	{
		return $this->idTokenHint;
	}

	/**
	 * @param string $loginHint
	 */
	public function setLoginHint($loginHint)
	{
		$this->loginHint = $loginHint;
	}
	
	public function getLoginHint()
	{
		return $this->loginHint;
	}

	/**
	 * @param string $acrValues
	 */
	public function setAcrValues($acrValues)
	{
		$this->acrValues = $acrValues;
	}
	
	public function getAcrValues()
	{
		return $this->acrValues;
	}
}
