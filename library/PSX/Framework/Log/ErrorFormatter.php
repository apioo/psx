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

use Monolog\Formatter\NormalizerFormatter;

/**
 * ErrorFormatter
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ErrorFormatter extends NormalizerFormatter
{
    public function format(array $record)
    {
        $message = $record['channel'] . '.' . $record['level_name'] . ': ';

        if (isset($record['context']['severity'])) {
            $severity = $this->getSeverity($record['context']['severity']);
            if (!empty($severity)) {
                $message.= $severity . ': ';
            }
        }

        $message.= $record['message'];

        if (isset($record['context']['file'])) {
            $message.= ' in ' . $record['context']['file'];
        }

        if (isset($record['context']['line'])) {
            $message.= ' on line ' . $record['context']['line'];
        }

        if (isset($record['context']['trace'])) {
            $message.= "\n" . 'Stack trace:' . "\n" . $record['context']['trace'];
        }

        return $message;
    }

    protected function getSeverity($severity)
    {
        switch ($severity) {
            case E_ERROR:
                return 'PHP Error';

            case E_CORE_ERROR:
                return 'PHP Core error';

            case E_COMPILE_ERROR:
                return 'PHP Compile error';

            case E_USER_ERROR:
                return 'PHP User error';

            case E_RECOVERABLE_ERROR:
                return 'PHP Recoverable error';

            case E_WARNING:
                return 'PHP Warning';

            case E_CORE_WARNING:
                return 'PHP Core warning';

            case E_COMPILE_WARNING:
                return 'PHP Compile warning';

            case E_USER_WARNING:
                return 'PHP User warning';

            case E_PARSE:
                return 'PHP Parse';

            case E_NOTICE:
                return 'PHP Notice';

            case E_USER_NOTICE:
                return 'PHP User notice';

            case E_STRICT:
                return 'PHP Strict';

            case E_DEPRECATED:
                return 'PHP Deprecated';

            case E_USER_DEPRECATED:
                return 'PHP User deprecated';

            default:
                return null;
        }
    }
}
