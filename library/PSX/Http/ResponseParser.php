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

namespace PSX\Http;

use PSX\Exception;
use PSX\Http\Stream\StringStream;

/**
 * ResponseParser
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResponseParser extends ParserAbstract
{
    /**
     * Converts an raw http response into an PSX\Http\Response object
     *
     * @param string $content
     * @return \PSX\Http\Response
     */
    public function parse($content)
    {
        $content = $this->normalize($content);

        list($scheme, $code, $message) = $this->getStatus($content);

        $response = new Response();
        $response->setProtocolVersion($scheme);
        $response->setStatus($code, $message);

        list($header, $body) = $this->splitMessage($content);

        $this->headerToArray($response, $header);

        $response->setBody(new StringStream($body));

        return $response;
    }

    protected function getStatus($response)
    {
        $line = $this->getStatusLine($response);

        if ($line !== false) {
            $parts = explode(' ', $line, 3);

            if (isset($parts[0]) && isset($parts[1]) && isset($parts[2])) {
                $scheme  = $parts[0];
                $code    = intval($parts[1]);
                $message = $parts[2];

                return array($scheme, $code, $message);
            } else {
                throw new ParseException('Invalid status line format');
            }
        } else {
            throw new ParseException('Couldnt find status line');
        }
    }

    public static function buildResponseFromHeader(array $headers)
    {
        $line = array_shift($headers);

        if (!empty($line)) {
            $parts = explode(' ', trim($line), 3);

            if (isset($parts[0]) && isset($parts[1]) && isset($parts[2])) {
                $scheme  = strval($parts[0]);
                $code    = intval($parts[1]);
                $message = strval($parts[2]);

                $response = new Response();
                $response->setProtocolVersion($scheme);
                $response->setStatus($code, $message);

                // append header
                foreach ($headers as $line) {
                    $parts = explode(':', $line, 2);

                    if (isset($parts[0]) && isset($parts[1])) {
                        $key   = $parts[0];
                        $value = trim($parts[1]);

                        $response->addHeader($key, $value);
                    }
                }

                return $response;
            } else {
                throw new ParseException('Invalid status line format');
            }
        } else {
            throw new ParseException('Couldnt find status line');
        }
    }

    /**
     * @param \PSX\Http\ResponseInterface $response
     * @return string
     */
    public static function buildStatusLine(ResponseInterface $response)
    {
        $protocol = $response->getProtocolVersion();
        $code     = $response->getStatusCode();
        $phrase   = $response->getReasonPhrase();

        if (empty($code)) {
            throw new \RuntimeException('Status code not set');
        }

        $protocol = !empty($protocol) ? $protocol : 'HTTP/1.1';

        if (empty($phrase) && isset(Http::$codes[$code])) {
            $phrase = Http::$codes[$code];
        }

        if (empty($phrase)) {
            throw new \RuntimeException('No reason phrase provided');
        }

        return $protocol . ' ' . $code . ' ' . $phrase;
    }

    /**
     * Parses an raw http response into an PSX\Http\Response object. Throws an
     * exception if the response has not an valid format
     *
     * @param string $content
     * @param integer $mode
     * @return \PSX\Http\Response
     */
    public static function convert($content, $mode = ParserAbstract::MODE_STRICT)
    {
        $parser = new self($mode);

        return $parser->parse($content);
    }
}
