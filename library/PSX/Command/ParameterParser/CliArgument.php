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

namespace PSX\Command\ParameterParser;

/**
 * CliArgument
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class CliArgument extends Map
{
    public function __construct($className, array $argv)
    {
        parent::__construct($className, $this->getArray($argv));
    }

    protected function getArray(array $argv)
    {
        $result = array();
        $len    = count($argv);

        for ($i = 0; $i < $len; $i++) {
            $name = $argv[$i];

            if (isset($name[0]) && $name[0] == '-') {
                $key = substr($name, 1);

                if (!empty($key)) {
                    if (isset($argv[$i + 1]) && isset($argv[$i + 1][0]) && $argv[$i + 1][0] == '-') {
                        $value = true;
                    } else {
                        $i++;
                        $value = isset($argv[$i]) ? $argv[$i] : null;
                    }

                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }
}
