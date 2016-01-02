<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2016 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Oauth\Provider\Data;

use PSX\Data\InvalidDataException;
use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;
use PSX\Oauth;
use PSX\Validate;

/**
 * Request
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Request extends RecordAbstract
{
    protected $consumerKey;
    protected $token;
    protected $signatureMethod;
    protected $signature;
    protected $timestamp;
    protected $nonce;
    protected $callback;
    protected $version;
    protected $verifier;

    public function getRecordInfo()
    {
        return new RecordInfo('request', array(
            'oauth_consumer_key'     => $this->consumerKey,
            'oauth_token'            => $this->token,
            'oauth_signature_method' => $this->signatureMethod,
            'oauth_signature'        => $this->signature,
            'oauth_timestamp'        => $this->timestamp,
            'oauth_nonce'            => $this->nonce,
            'oauth_callback'         => $this->callback,
            'oauth_version'          => $this->version,
            'oauth_verifier'         => $this->verifier
        ));
    }

    public function setConsumerKey($consumerKey)
    {
        $this->consumerKey = $consumerKey;
    }
    
    public function getConsumerKey()
    {
        return $this->consumerKey;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }
    
    public function getToken()
    {
        return $this->token;
    }

    public function setSignatureMethod($signatureMethod)
    {
        switch ($signatureMethod) {
            case 'HMAC-SHA1':
            case 'RSA-SHA1':
            case 'PLAINTEXT':
                $this->signatureMethod = $signatureMethod;
                break;

            default:
                throw new InvalidDataException('Invalid signature method');
                break;
        }
    }

    public function getSignatureMethod()
    {
        return $this->signatureMethod;
    }

    public function setSignature($signature)
    {
        $this->signature = $signature;
    }

    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param integer $timestamp
     */
    public function setTimestamp($timestamp)
    {
        if (is_numeric($timestamp) && strlen($timestamp) == 10) {
            $this->timestamp = $timestamp;
        } else {
            throw new InvalidDataException('Invalid timestamp format');
        }
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function setNonce($nonce)
    {
        $this->nonce = $nonce;
    }

    public function getNonce()
    {
        return $this->nonce;
    }

    public function setCallback($callback)
    {
        if ($callback == 'oob') {
            // callback was set "out of bound" ... we get the url later from the
            // consumer object
            $this->callback = 'oob';
        } elseif (strlen($callback) >= 7 && strlen($callback) <= 256 && filter_var($callback, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
            $this->callback = $callback;
        } else {
            throw new InvalidDataException('Invalid callback format');
        }
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVerifier($verifier)
    {
        if (strlen($verifier) >= 16 && strlen($verifier) <= 512) {
            $this->verifier = $verifier;
        } else {
            throw new InvalidDataException('Invalid verifier format');
        }
    }

    public function getVerifier()
    {
        return $this->verifier;
    }
}
