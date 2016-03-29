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

namespace PSX\Uri;

use InvalidArgumentException;

/**
 * UriResolver
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     http://www.ietf.org/rfc/rfc3986.txt
 */
class UriResolver
{
    /**
     * Resolves a base uri against a target uri
     *
     * @param \PSX\Uri\Uri $baseUri
     * @param \PSX\Uri\Uri $targetUri
     * @return \PSX\Uri\Uri
     */
    public static function resolve(Uri $baseUri, Uri $targetUri)
    {
        if (!$baseUri->isAbsolute()) {
            throw new InvalidArgumentException('Base uri must be absolute');
        }

        // if the target uri is absolute
        if ($targetUri->isAbsolute()) {
            $path = $targetUri->getPath();

            if (!empty($path)) {
                return $targetUri->withPath(self::removeDotSegments($path));
            } else {
                return $targetUri;
            }
        } else {
            $authority = $targetUri->getAuthority();
            $path      = $targetUri->getPath();
            $query     = $targetUri->getQuery();

            if (!empty($authority)) {
                if (!empty($path)) {
                    $path = self::removeDotSegments($path);
                }
            } else {
                if (empty($path)) {
                    if (empty($query)) {
                        $path  = $baseUri->getPath();
                        $query = $baseUri->getQuery();
                    } else {
                        $path = self::merge($baseUri->getPath(), '');
                    }
                } else {
                    if (substr($path, 0, 1) == '/') {
                        $path = self::removeDotSegments($path);
                    } else {
                        $path = self::merge($baseUri->getPath(), $path);
                        $path = self::removeDotSegments($path);
                    }
                }

                $authority = $baseUri->getAuthority();
            }

            return new Uri(
                $baseUri->getScheme(),
                $authority,
                $path,
                $query,
                $targetUri->getFragment()
            );
        }
    }

    /**
     * @param string $relativePath
     * @return string
     */
    public static function removeDotSegments($relativePath)
    {
        $parts = explode('/', $relativePath);
        $path  = array();

        foreach ($parts as $part) {
            $part = trim($part);

            if (empty($part) || $part == '.') {
            } elseif ($part == '..') {
                array_pop($path);
            } else {
                $path[] = $part;
            }
        }

        $resolvedPath = implode('/', $path);

        if (substr($relativePath, 0, 1) == '/') {
            $resolvedPath = '/' . $resolvedPath;
        }

        if (trim($resolvedPath, '/') != '' && (
            substr($relativePath, -1) == '/' ||
            substr($relativePath, -2) == '/.' ||
            substr($relativePath, -3) == '/..')) {
            $resolvedPath = $resolvedPath . '/';
        }

        return $resolvedPath;
    }

    /**
     * Percent encodes a value
     *
     * @param string $value
     * @param boolean $preventDoubleEncode
     * @return string
     */
    public static function percentEncode($value, $preventDoubleEncode = true)
    {
        $len = strlen($value);
        $val = '';

        for ($i = 0; $i < $len; $i++) {
            $j = ord($value[$i]);

            if ($j <= 0xFF) {
                // check for double encoding
                if ($preventDoubleEncode) {
                    if ($j == 0x25 && $i < $len - 2) {
                        $hex = strtoupper(substr($value, $i + 1, 2));

                        if (ctype_xdigit($hex)) {
                            $val.= '%' . $hex;

                            $i+= 2;
                            continue;
                        }
                    }
                }

                // escape characters
                if (($j >= 0x41 && $j <= 0x5A) || // alpha
                    ($j >= 0x61 && $j <= 0x7A) || // alpha
                    ($j >= 0x30 && $j <= 0x39) || // digit
                    $j == 0x2D || // hyphen
                    $j == 0x2E || // period
                    $j == 0x5F || // underscore
                    $j == 0x7E) {
                    // tilde

                    $val.= $value[$i];
                } else {
                    $hex = dechex($j);
                    $hex = $j <= 0xF ? '0' . $hex : $hex;

                    $val.= '%' . strtoupper($hex);
                }
            } else {
                $val.= $value[$i];
            }
        }

        return $val;
    }

    protected static function merge($basePath, $targetPath)
    {
        $pos = strrpos($basePath, '/');

        if ($pos !== false) {
            return substr($basePath, 0, $pos + 1) . $targetPath;
        } else {
            return $targetPath;
        }
    }
}
