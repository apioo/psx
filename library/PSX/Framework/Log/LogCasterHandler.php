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

namespace PSX\Framework\Log;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

/**
 * A monolog handler to send messages to an logcaster server. See
 * https://github.com/k42b3/logcaster for more informations.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class LogCasterHandler extends AbstractProcessingHandler
{
    protected $socket;
    protected $isAvailable;

    public function __construct($host = '127.0.0.1', $port = 61613, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->connect($host, $port);
    }

    public function close()
    {
        parent::close();

        $this->disconnect();
    }

    protected function write(array $record)
    {
        if (!$this->isAvailable()) {
            return;
        }

        $message = $record['channel'] . '.' . $record['level_name'] . ': ';
        $message.= $record['message'];

        if (isset($record['context']['file'])) {
            $message.= ' in ' . $record['context']['file'];
        }

        if (isset($record['context']['line'])) {
            $message.= ' on line ' . $record['context']['line'];
        }

        $this->writeLn($message, $this->getLevelColor($record['level_name']));

        $trace = isset($record['context']['trace']) ? $record['context']['trace'] : '';

        if (!empty($trace)) {
            $this->writeLn($trace, '#666');
        }
    }

    protected function connect($host, $port)
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        set_error_handler(__CLASS__ . '::handleError');
        $this->isAvailable = socket_connect($this->socket, $host, $port);
        restore_error_handler();
    }

    protected function disconnect()
    {
        if (!$this->isAvailable()) {
            return;
        }

        socket_close($this->socket);
    }

    protected function writeLn($message, $color = '#000', $bold = false)
    {
        $body = json_encode(array(
            'color'   => $color,
            'bold'    => (bool) $bold,
            'message' => $message,
        ));

        socket_write($this->socket, $body . "\n");
    }

    protected function isAvailable()
    {
        return $this->isAvailable;
    }

    protected function getLevelColor($level)
    {
        switch ($level) {
            case 'ERROR':
            case 'CRITICAL':
            case 'ALERT':
            case 'EMERGENCY':
                return '#F00';

            case 'NOTICE':
                return '#00F';

            default:
                return '#000';
        }
    }

    public static function handleError($errno, $errstr)
    {
        return -1;
    }
}
