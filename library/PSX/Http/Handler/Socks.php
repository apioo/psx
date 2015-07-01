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

namespace PSX\Http\Handler;

use PSX\Exception;
use PSX\Http;
use PSX\Http\HandlerException;
use PSX\Http\HandlerInterface;
use PSX\Http\NotSupportedException;
use PSX\Http\Options;
use PSX\Http\RequestInterface;
use PSX\Http\ResponseParser;
use PSX\Http\Stream\SocksStream;
use PSX\Http\Stream\StringStream;

/**
 * This handler writes the complete HTTP message by itself on the socket
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Socks implements HandlerInterface
{
    protected $chunkSize = 8192;

    /**
     * The chunk size which is used if the transfer encoding is "chunked"
     *
     * @param integer $chunkSize
     */
    public function setChunkSize($chunkSize)
    {
        $this->chunkSize = $chunkSize;
    }

    public function request(RequestInterface $request, Options $options)
    {
        $context = stream_context_create();

        // ssl
        $scheme = null;

        if ($options->getSsl() !== false && ($options->getSsl() === true || strcasecmp($request->getUri()->getScheme(), 'https') === 0)) {
            $transports = stream_get_transports();

            if (in_array('tls', $transports)) {
                $scheme = 'tls';
            } elseif (in_array('ssl', $transports)) {
                $scheme = 'ssl';
            } else {
                throw new NotSupportedException('https is not supported');
            }

            Stream::assignSslContext($context, $options);
        } else {
            $scheme = 'tcp';
        }

        // port
        $port = $request->getUri()->getPort();

        if (empty($port)) {
            $port = getservbyname($request->getUri()->getScheme(), 'tcp');
        }

        // open socket
        set_error_handler(__CLASS__ . '::handleError');
        $timeout = ini_get('default_socket_timeout');
        $handle  = stream_socket_client($scheme . '://' . $request->getUri()->getHost() . ':' . $port, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context);
        restore_error_handler();

        if ($handle !== false) {
            // timeout
            $timeout = $options->getTimeout();

            if (!empty($timeout)) {
                stream_set_timeout($handle, $timeout);
            }

            // callback
            $callback = $options->getCallback();

            if (!empty($callback)) {
                call_user_func_array($callback, array($handle, $request));
            }

            // write header
            $headers = ResponseParser::buildHeaderFromMessage($request);

            fwrite($handle, Http\ParserAbstract::buildStatusLine($request) . Http::$newLine);

            foreach ($headers as $header) {
                fwrite($handle, $header . Http::$newLine);
            }

            fwrite($handle, Http::$newLine);
            fflush($handle);

            // write body
            $body = $request->getBody();

            if ($body !== null && !in_array($request->getMethod(), array('HEAD', 'GET'))) {
                if ($request->getHeader('Transfer-Encoding') == 'chunked') {
                    while (!$body->eof()) {
                        $chunk = $body->read($this->chunkSize);
                        $len   = strlen($chunk);

                        if ($len > 0) {
                            fwrite($handle, dechex($len) . Http::$newLine . $chunk . Http::$newLine);
                            fflush($handle);
                        }
                    }

                    fwrite($handle, '0' . Http::$newLine . Http::$newLine);
                    fflush($handle);
                } else {
                    fwrite($handle, (string) $body);
                    fflush($handle);
                }
            }

            // read header
            $headers = array();

            do {
                $header = trim(fgets($handle));

                if (!empty($header)) {
                    $headers[] = $header;
                }
            } while (!empty($header));

            // check for timeout
            $meta = stream_get_meta_data($handle);

            if ($meta['timed_out']) {
                throw new HandlerException('Connection timeout');
            }

            // build response
            $response = ResponseParser::buildResponseFromHeader($headers);

            // create stream
            $contentLength   = (int) $response->getHeader('Content-Length');
            $chunkedEncoding = $response->getHeader('Transfer-Encoding') == 'chunked';

            if ($request->getMethod() != 'HEAD') {
                $response->setBody(new SocksStream($handle, $contentLength, $chunkedEncoding));
            } else {
                fclose($handle);

                $response->setBody(new StringStream());
            }

            return $response;
        } else {
            throw new HandlerException(!empty($errstr) ? $errstr : 'Could not open socket');
        }
    }

    public static function handleError($errno, $errstr)
    {
        restore_error_handler();

        throw new HandlerException($errstr, $errno);
    }
}
