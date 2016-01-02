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

namespace PSX\OpenSsl;

use InvalidArgumentException;

/**
 * PKey
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class PKey
{
    use ErrorHandleTrait;

    protected $res;

    public function __construct($configargs = array())
    {
        if (is_array($configargs)) {
            $res = openssl_pkey_new($configargs);

            self::handleReturn($res);

            $this->res = $res;
        } elseif (is_resource($configargs)) {
            $this->res = $configargs;
        } else {
            throw new InvalidArgumentException('Must be either an array or a resource');
        }
    }

    public function free()
    {
        openssl_pkey_free($this->res);
    }

    public function getDetails()
    {
        $details = openssl_pkey_get_details($this->res);

        self::handleReturn($details);

        return $details;
    }

    public function getPublicKey()
    {
        $details = $this->getDetails();

        return isset($details['key']) ? $details['key'] : null;
    }

    public function getResource()
    {
        return $this->res;
    }

    public function export(&$out, $passphrase = null, array $configargs = array())
    {
        $result = openssl_pkey_export($this->res, $out, $passphrase, $configargs);

        self::handleReturn($result);

        return $result;
    }

    public static function getPrivate($key, $passphrase = null)
    {
        $res = openssl_pkey_get_private($key, $passphrase);

        self::handleReturn($res);

        return new self($res);
    }

    public static function getPublic($certificate)
    {
        $res = openssl_pkey_get_public($certificate);

        self::handleReturn($res);

        return new self($res);
    }
}
