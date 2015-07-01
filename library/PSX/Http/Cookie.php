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

use InvalidArgumentException;
use PSX\DateTime;

/**
 * Cookie
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Cookie
{
    private $name;
    private $value;
    private $expires;
    private $path;
    private $domain;
    private $secure;
    private $httponly;

    public function __construct($name, $value = null, \DateTime $expires = null, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        if (func_num_args() == 1) {
            $this->parse($name);
        } else {
            $this->name     = $name;
            $this->value    = $value;
            $this->expires  = $expires;
            $this->path     = $path;
            $this->domain   = $domain;
            $this->secure   = $secure;
            $this->httponly = $httponly;
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getExpires()
    {
        return $this->expires;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getDomain()
    {
        return $this->domain;
    }

    public function getSecure()
    {
        return $this->secure;
    }

    public function getHttponly()
    {
        return $this->httponly;
    }

    public function toString()
    {
        $parameters = array(
            $this->name => $this->value
        );

        if ($this->expires !== null) {
            $parameters['Expires'] = $this->expires->format(DateTime::HTTP);
        }

        if (!empty($this->path)) {
            $parameters['Path'] = $this->path;
        }

        if (!empty($this->domain)) {
            $parameters['Domain'] = $this->domain;
        }

        if ($this->secure) {
            $parameters['Secure'] = null;
        }

        if ($this->httponly) {
            $parameters['HttpOnly'] = null;
        }

        $cookie = [];
        foreach ($parameters as $key => $value) {
            if ($value === null) {
                $cookie[] = $key;
            } else {
                $cookie[] = $key . '=' . $value;
            }
        }

        return implode('; ', $cookie);
    }

    public function __toString()
    {
        return $this->toString();
    }

    protected function parse($cookie)
    {
        $cookie = (string) $cookie;
        $parts  = explode(';', $cookie);

        // get cookie key value pair
        $part = array_shift($parts);
        $kv   = explode('=', $part, 2);

        $this->name  = isset($kv[0]) ? $kv[0] : null;
        $this->value = isset($kv[1]) ? $kv[1] : null;

        if (empty($this->name)) {
            throw new InvalidArgumentException('Invalid cookie format');
        }

        // get cookie attributes
        foreach ($parts as $part) {
            $kv  = explode('=', $part, 2);
            $key = isset($kv[0]) ? trim($kv[0]) : null;
            $key = strtolower($key);
            $val = isset($kv[1]) ? $kv[1] : null;

            switch ($key) {
                case 'domain':
                    // remove leading dot
                    if (isset($val[0]) && $val[0] == '.') {
                        $val = substr($val, 1);
                    }

                    $this->domain = $val;
                    break;

                case 'path':
                    $this->path = $val;
                    break;

                case 'expires':
                    $this->expires = new \DateTime($val);
                    break;

                case 'secure':
                    $this->secure = true;
                    break;

                case 'httponly':
                    $this->httponly = true;
                    break;
            }
        }
    }

    /**
     * Converts an Cookie header into an array of cookie objects
     *
     * @param string $cookieList
     * @return \PSX\Http\Cookie[]
     */
    public static function parseList($cookieList)
    {
        $parts   = explode(';', $cookieList);
        $cookies = array();

        foreach ($parts as $part) {
            try {
                $cookies[] = new self(trim($part));
            } catch (InvalidArgumentException $e) {
            }
        }

        return $cookies;
    }
}
