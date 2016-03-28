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

namespace PSX\Framework\Session;

use SessionHandlerInterface;

/**
 * Session implementation wich actually doesnt start a session. Useful for cli
 * applications where it is not possible to start a session
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Memory extends Session
{
    protected $container = array();

    public function __construct($name, SessionHandlerInterface $handler = null)
    {
        $this->setSessionTokenKey(__CLASS__);
        $this->setName($name);
        $this->setToken(md5($name));
    }

    public function set($key, $value)
    {
        $this->container[$key] = $value;
    }

    public function get($key)
    {
        return isset($this->container[$key]) ? $this->container[$key] : null;
    }

    public function has($key)
    {
        return isset($this->container[$key]);
    }

    public function setSessionTokenKey($tokenKey)
    {
        $this->sessionTokenKey = $tokenKey;
    }

    public function getSessionTokenKey()
    {
        return $this->sessionTokenKey;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setId($id)
    {
    }

    public function getId()
    {
    }

    public function setSaveHandler(SessionHandlerInterface $handler)
    {
    }

    public function setSavePath($path)
    {
    }

    public function start()
    {
    }

    public function close()
    {
    }

    public function destroy()
    {
    }

    public function isActive()
    {
        return true;
    }
}
