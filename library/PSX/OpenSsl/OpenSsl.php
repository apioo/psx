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

/**
 * OpenSsl
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class OpenSsl
{
    use ErrorHandleTrait;

    public static function decrypt($data, $method, $password, $rawInput = false, $iv = '')
    {
        $return = openssl_decrypt($data, $method, $password, $rawInput, $iv);

        self::handleReturn($return);

        return $return;
    }

    public static function dhComputeKey($pubKey, PKey $dhKey)
    {
        $return = openssl_dh_compute_key($pubKey, $dhKey->getResource());

        self::handleReturn($return);

        return $return;
    }

    public static function digest($data, $func, $rawOutput = false)
    {
        $return = openssl_digest($data, $func, $rawOutput);

        self::handleReturn($return);

        return $return;
    }

    public static function encrypt($data, $method, $password, $rawOutput = false, $iv = '')
    {
        $return = openssl_encrypt($data, $method, $password, $rawOutput, $iv);

        self::handleReturn($return);

        return $return;
    }

    public static function errorString()
    {
        return openssl_error_string();
    }

    public static function getCipherMethods($aliases = false)
    {
        return openssl_get_cipher_methods($aliases);
    }

    public static function getMdMethods($aliases = false)
    {
        return openssl_get_md_methods($aliases);
    }

    public static function open($sealedData, &$openData, $envKey, PKey $key)
    {
        $return = openssl_open($sealedData, $openData, $envKey, $key->getResource());

        self::handleReturn($return);

        return $return;
    }

    public static function seal($data, &$sealedData, &$envKeys, array $pubKeys)
    {
        $pubKeyIds = array();
        foreach ($pubKeys as $pubKey) {
            if ($pubKey instanceof PKey) {
                $pubKeyIds[] = $pubKey->getPublicKey();
            } else {
                throw new Exception('Pub keys must be an array containing PSX\OpenSsl\PKey instances');
            }
        }

        $return = openssl_seal($data, $sealedData, $envKeys, $pubKeyIds);

        self::handleReturn($return);

        return $return;
    }

    public static function sign($data, &$signature, PKey $key, $signatureAlg = OPENSSL_ALGO_SHA1)
    {
        $return = openssl_sign($data, $signature, $key->getResource(), $signatureAlg);

        self::handleReturn($return);

        return $return;
    }

    public static function verify($data, $signature, PKey $key, $signatureAlg = OPENSSL_ALGO_SHA1)
    {
        $return = openssl_verify($data, $signature, $key->getPublicKey(), $signatureAlg);

        self::handleReturn($return);

        return $return;
    }

    public static function privateDecrypt($data, &$decrypted, PKey $key, $padding = OPENSSL_PKCS1_PADDING)
    {
        $return = openssl_private_decrypt($data, $decrypted, $key->getResource(), $padding);

        self::handleReturn($return);

        return $return;
    }

    public static function privateEncrypt($data, &$crypted, PKey $key, $padding = OPENSSL_PKCS1_PADDING)
    {
        $return = openssl_private_encrypt($data, $crypted, $key->getResource(), $padding);

        self::handleReturn($return);

        return $return;
    }

    public static function publicDecrypt($data, &$decrypted, PKey $key, $padding = OPENSSL_PKCS1_PADDING)
    {
        $return = openssl_public_decrypt($data, $decrypted, $key->getPublicKey(), $padding);

        self::handleReturn($return);

        return $return;
    }

    public static function publicEncrypt($data, &$crypted, PKey $key, $padding = OPENSSL_PKCS1_PADDING)
    {
        $return = openssl_public_encrypt($data, $crypted, $key->getPublicKey(), $padding);

        self::handleReturn($return);

        return $return;
    }

    public static function randomPseudoBytes($length)
    {
        return openssl_random_pseudo_bytes($length);
    }
}
