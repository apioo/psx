<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Http;

use PSX\Exception;
use PSX\Http;

/**
 * Response
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Response extends Message implements ResponseInterface
{
    protected $code;
    protected $reasonPhrase;

    /**
     * @param integer $code
     * @param array $headers
     * @param string $body
     */
    public function __construct($code = null, array $headers = array(), $body = null)
    {
        parent::__construct($headers, $body);

        $this->code = $code;
    }

    /**
     * Returns the http response code
     *
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->code;
    }

    /**
     * Returns the http response message. That means the last part of the status
     * line i.e. "OK" from an 200 response
     *
     * @return string
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * Sets the status code and reason phrase. If no reason phrase is provided
     * the standard message according to the status code is used
     *
     * @param integer $code
     * @param integer $reasonPhrase
     */
    public function setStatus($code, $reasonPhrase = null)
    {
        $this->code = (int) $code;

        if ($reasonPhrase !== null) {
            $this->reasonPhrase = $reasonPhrase;
        } elseif (isset(Http::$codes[$this->code])) {
            $this->reasonPhrase = Http::$codes[$this->code];
        }
    }

    /**
     * Converts the response object to an http response string
     *
     * @return string
     */
    public function toString()
    {
        $response = ResponseParser::buildStatusLine($this) . Http::$newLine;
        $headers  = ResponseParser::buildHeaderFromMessage($this);

        foreach ($headers as $header) {
            $response.= $header . Http::$newLine;
        }

        $response.= Http::$newLine;
        $response.= (string) $this->getBody();

        return $response;
    }

    public function __toString()
    {
        return $this->toString();
    }
}
