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

namespace PSX\Framework\Dispatch;

use PSX\Framework\Config\Config;
use PSX\Http\Request;
use PSX\Http\Stream\BufferedStream;
use PSX\Http\Stream\TempStream;
use PSX\Uri\Url;
use UnexpectedValueException;

/**
 * RequestFactory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class RequestFactory implements RequestFactoryInterface
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * If the psx_url from the config contains an path in the url this path gets
     * removed from the request url. Because of this you can have an psx project
     * also in an sub folder
     *
     * @return \PSX\Http\RequestInterface
     */
    public function createRequest()
    {
        $parts = parse_url($this->config['psx_url']);

        if ($parts !== false && isset($parts['host'])) {
            $scheme = isset($parts['scheme']) ? $parts['scheme'] : null;
            if (empty($scheme)) {
                $https  = isset($_SERVER['HTTPS']) ? strtolower($_SERVER['HTTPS']) : null;
                $scheme = !empty($https) && $https != 'off' ? 'https' : 'http';
            }

            $port = !empty($parts['port']) ? ':' . $parts['port'] : '';

            if (!$this->isCli()) {
                $path = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
                $skip = (isset($parts['path']) ? $parts['path'] : '') . '/' . $this->config['psx_dispatch'];
                $path = $this->skip($path, $skip);
            } else {
                $path = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';
            }

            $path = '/' . ltrim($path, '/');
            $self = $scheme . '://' . $parts['host'] . $port . $path;

            // create request
            $url     = new Url($self);
            $method  = $this->getRequestMethod();
            $headers = $this->getRequestHeaders();
            $body    = null;

            // create body
            $requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : null;

            if (in_array($requestMethod, array('POST', 'PUT', 'DELETE', 'PATCH'))) {
                // as long as we support PHP < 5.6 we must use a BufferedStream
                // since the php://input stream can only be read once
                $body = new BufferedStream(new TempStream(fopen('php://input', 'r')));
            }

            return new Request($url, $method, $headers, $body);
        } else {
            throw new UnexpectedValueException('Invalid PSX url');
        }
    }

    /**
     * Tries to detect the current request method. It considers the
     * X-HTTP-METHOD-OVERRIDE header.
     *
     * @return string
     */
    protected function getRequestMethod()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            // check for X-HTTP-Method-Override
            if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']) &&
                in_array($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'], array('OPTIONS', 'GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'PATCH'))) {
                return $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
            } else {
                return $_SERVER['REQUEST_METHOD'];
            }
        } else {
            return 'GET';
        }
    }

    /**
     * Returns all request headers
     *
     * @return array
     */
    protected function getRequestHeaders()
    {
        $contentKeys = array('CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true);
        $headers     = array();

        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headers[str_replace('_', '-', substr($key, 5))] = $value;
            } elseif (isset($contentKeys[$key])) {
                $headers[str_replace('_', '-', $key)] = $value;
            }
        }

        if (!isset($headers['AUTHORIZATION'])) {
            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
                $headers['AUTHORIZATION'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . (isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : ''));
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['AUTHORIZATION'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }

        return $headers;
    }

    /**
     * Returns whether we are in CLI mode or not
     *
     * @return boolean
     */
    protected function isCli()
    {
        return PHP_SAPI == 'cli';
    }

    /**
     * Removes the given $skipPath from the $srcPath as long as they have the
     * same value
     *
     * @param string $srcPath
     * @param string $skipPath
     * @return string
     */
    protected function skip($srcPath, $skipPath)
    {
        $len = strlen($srcPath);

        for ($i = 0; $i < $len; $i++) {
            if (!isset($skipPath[$i]) || $skipPath[$i] != $srcPath[$i]) {
                break;
            }
        }

        return substr($srcPath, $i);
    }
}
