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

use InvalidArgumentException;
use PSX\Exception;
use PSX\Http;

/**
 * ParserAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class ParserAbstract
{
    const MODE_STRICT = 0x1;
    const MODE_LOOSE  = 0x2;

    protected $mode;

    /**
     * The mode indicates how the header is detected in strict mode we search
     * exactly for CRLF CRLF in loose mode we look for the first empty line. In
     * loose mode we can parse an header wich was defined in the code means is
     * not strictly seperated by CRLF
     *
     * @param integer $mode
     */
    public function __construct($mode = self::MODE_STRICT)
    {
        if ($mode == self::MODE_STRICT || $mode == self::MODE_LOOSE) {
            $this->mode = $mode;
        } else {
            throw new InvalidArgumentException('Invalid parse mode');
        }
    }

    /**
     * Converts an raw http message into an PSX\Http\Message object
     *
     * @param string $content
     * @return \PSX\Http\Message
     */
    abstract public function parse($content);

    /**
     * Splits an given http message into the header and body part
     *
     * @param string $message
     * @return array
     */
    protected function splitMessage($message)
    {
        if ($this->mode == self::MODE_STRICT) {
            $pos    = strpos($message, Http::$newLine . Http::$newLine);
            $header = substr($message, 0, $pos);
            $body   = trim(substr($message, $pos + 1));
        } elseif ($this->mode == self::MODE_LOOSE) {
            $lines  = explode("\n", $message);
            $header = '';
            $body   = '';
            $found  = false;
            $count  = count($lines);

            foreach ($lines as $i => $line) {
                $line = trim($line);

                if (!$found && empty($line)) {
                    $found = true;
                    continue;
                }

                if (!$found) {
                    $header.= $line . Http::$newLine;
                } else {
                    $body.= $line . ($i < $count - 1 ? "\n" : '');
                }
            }
        }

        return array($header, $body);
    }

    /**
     * @param string $content
     * @return string
     */
    protected function normalize($content)
    {
        if (empty($content)) {
            throw new InvalidArgumentException('Empty message');
        }

        if ($this->mode == self::MODE_LOOSE) {
            $content = str_replace(array("\r\n", "\n", "\r"), "\n", $content);
        }

        return $content;
    }

    /**
     * Parses an raw http header string into an Message object
     *
     * @param \PSX\Http\Message $message
     * @param string $header
     * @return array
     */
    protected function headerToArray(Message $message, $header)
    {
        $lines = explode(Http::$newLine, $header);

        foreach ($lines as $line) {
            $parts = explode(':', $line, 2);

            if (isset($parts[0]) && isset($parts[1])) {
                $key   = $parts[0];
                $value = substr($parts[1], 1);

                $message->addHeader($key, $value);
            }
        }
    }

    protected function getStatusLine($message)
    {
        if ($this->mode == self::MODE_STRICT) {
            $pos = strpos($message, Http::$newLine);
        } elseif ($this->mode == self::MODE_LOOSE) {
            $pos = strpos($message, "\n");
        }

        return $pos !== false ? substr($message, 0, $pos) : false;
    }

    /**
     * @param \PSX\Http\MessageInterface $message
     * @return array
     */
    public static function buildHeaderFromMessage(MessageInterface $message)
    {
        $headers = $message->getHeaders();
        $result  = array();

        foreach ($headers as $key => $value) {
            if ($key == 'set-cookie') {
                foreach ($value as $cookie) {
                    $result[] = $key . ': ' . $cookie;
                }
            } else {
                $result[] = $key . ': ' . implode(', ', $value);
            }
        }

        return $result;
    }
}
