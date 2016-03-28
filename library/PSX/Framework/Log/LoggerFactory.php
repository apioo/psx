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

use Monolog\Handler as MonologHandler;
use Monolog\Logger;
use Monolog\Processor as MonologProcessor;

/**
 * LoggerFactory
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class LoggerFactory
{
    /**
     * @param integer $level
     * @param string $handlerName
     * @param string $uri
     * @return \Psr\Log\LoggerInterface
     */
    public static function factory($level, $handlerName, $uri)
    {
        $level = !empty($level) ? $level : Logger::ERROR;

        switch ($handlerName) {
            case 'stream':
                $handler = new MonologHandler\StreamHandler($uri, $level);
                break;

            case 'logcaster':
                $host = parse_url($uri, PHP_URL_HOST) ?: '127.0.0.1';
                $port = parse_url($uri, PHP_URL_PORT) ?: 61613;

                $handler = new LogCasterHandler($host, $port, $level);
                break;

            case 'void':
                $handler = new MonologHandler\NullHandler($level);
                break;

            case 'system':
            default:
                $handler = new MonologHandler\ErrorLogHandler(MonologHandler\ErrorLogHandler::OPERATING_SYSTEM, $level, true, true);
                $handler->setFormatter(new ErrorFormatter());
                break;
        }

        $logger = new Logger('psx');
        $logger->pushHandler($handler);

        return $logger;
    }
}
