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
 * Session
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Session
{
    protected $name;
    protected $token;

    protected $sessionTokenKey;

    public function __construct($name, SessionHandlerInterface $handler = null)
    {
        $this->setSessionTokenKey(__CLASS__);
        $this->setName($name);

        $this->generateToken();

        if ($handler !== null) {
            $this->setSaveHandler($handler);
        }
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    public function __get($key)
    {
        return $this->get($key);
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
        session_name($this->name = $name);
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

    /**
     * @codeCoverageIgnore
     * @param string $id
     */
    public function setId($id)
    {
        session_id($id);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * @codeCoverageIgnore
     * @param \SessionHandlerInterface $handler
     */
    public function setSaveHandler(SessionHandlerInterface $handler)
    {
        session_set_save_handler($handler);
    }

    /**
     * @codeCoverageIgnore
     * @param string $path
     */
    public function setSavePath($path)
    {
        session_save_path($path);
    }

    /**
     * @codeCoverageIgnore
     */
    public function start()
    {
        session_start();

        if (isset($_SESSION[$this->sessionTokenKey]) && $_SESSION[$this->sessionTokenKey] === $this->token) {
            // we have an valid token
        } else {
            $_SESSION = array();
            $_SESSION[$this->sessionTokenKey] = $this->token;
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function close()
    {
        session_write_close();
    }

    /**
     * @codeCoverageIgnore
     */
    public function destroy()
    {
        $_SESSION = array();

        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600);
        }

        if ($this->isActive()) {
            session_destroy();
        }
    }

    public function isActive()
    {
        $sessId = $this->getId();

        return !empty($sessId);
    }

    protected function generateToken()
    {
        $ip = isset($_SERVER['REMOTE_ADDR'])     ? $_SERVER['REMOTE_ADDR']     : '0.0.0.0';
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'undefined';

        $this->setToken(md5($this->name . $ip . $ua));
    }
}
