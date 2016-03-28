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

namespace PSX\Framework\Dispatch\Sender;

use PSX\Framework\Dispatch\SenderInterface;
use PSX\Http\Http;
use PSX\Http\ResponseInterface;
use PSX\Http\ResponseParser;
use PSX\Http\Stream\FileStream;
use PSX\Http\Stream\StringStream;

/**
 * Basic sender which handles file stream bodies, content encoding and transfer
 * encoding
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Basic implements SenderInterface
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

    public function send(ResponseInterface $response)
    {
        // remove body on specific status codes
        if (in_array($response->getStatusCode(), array(100, 101, 204, 304))) {
            $response->setBody(new StringStream(''));
        }
        // if we have a location header we dont send any content
        elseif ($response->hasHeader('Location')) {
            $response->setBody(new StringStream(''));
        }

        if ($this->shouldSendHeader()) {
            // if we have a file stream body set custom header
            $this->prepareFileStream($response);

            // send status line
            $this->sendStatusLine($response);

            // send headers
            $this->sendHeaders($response);
        }

        // send body
        $this->sendBody($response);
    }

    protected function shouldSendHeader()
    {
        return PHP_SAPI != 'cli' && !headers_sent();
    }

    protected function prepareFileStream(ResponseInterface $response)
    {
        if ($response->getBody() instanceof FileStream) {
            $fileName = $response->getBody()->getFileName();
            if (empty($fileName)) {
                $fileName = 'file';
            }

            $contentType = $response->getBody()->getContentType();
            if (empty($contentType)) {
                $contentType = 'application/octet-stream';
            }

            $response->setHeader('Content-Type', $contentType);
            $response->setHeader('Content-Disposition', 'attachment; filename="' . addcslashes($fileName, '"') . '"');
            $response->setHeader('Transfer-Encoding', 'chunked');
        }
    }

    protected function sendStatusLine(ResponseInterface $response)
    {
        $scheme = $response->getProtocolVersion();
        if (empty($scheme)) {
            $scheme = 'HTTP/1.1';
        }

        $code = $response->getStatusCode();
        if (!isset(Http::$codes[$code])) {
            $code = 200;
        }

        $this->sendHeader($scheme . ' ' . $code . ' ' . Http::$codes[$code]);
    }

    protected function sendHeaders(ResponseInterface $response)
    {
        $headers = ResponseParser::buildHeaderFromMessage($response);

        foreach ($headers as $header) {
            $this->sendHeader($header);
        }
    }

    protected function sendHeader($header)
    {
        header($header);
    }

    protected function sendBody(ResponseInterface $response)
    {
        if ($response->getBody() !== null) {
            $transferEncoding = $response->getHeader('Transfer-Encoding');
            $contentEncoding  = $response->getHeader('Content-Encoding');

            if ($transferEncoding == 'chunked') {
                $this->sendContentChunked($response);
            } else {
                $this->sendContentEncoded($contentEncoding, $response);
            }
        }
    }

    protected function sendContentEncoded($contentEncoding, ResponseInterface $response)
    {
        switch ($contentEncoding) {
            case 'deflate':
                $body = (string) $response->getBody();

                echo gzcompress($body);
                break;

            case 'gzip':
            case 'x-gzip':
                $body = (string) $response->getBody();

                echo gzencode($body);
                break;

            default:
                echo (string) $response->getBody();
                break;
        }
    }

    protected function sendContentChunked(ResponseInterface $response)
    {
        $body = $response->getBody();
        $body->seek(0);

        while (!$body->eof()) {
            $chunk = $body->read($this->chunkSize);
            $len   = mb_strlen($chunk);

            if ($len > 0) {
                echo dechex($len) . "\r\n" . $chunk . "\r\n";
                flush();
            }
        }

        echo '0' . "\r\n" . "\r\n";
        flush();

        $body->close();
    }
}
