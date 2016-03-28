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

namespace PSX\Sql\Provider\PDO;

use PDO;
use PSX\Sql\Provider\ParameterResolver;

/**
 * PDOAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class PDOAbstract
{
    protected $pdo;
    protected $sql;
    protected $parameters;
    protected $definition;
    protected $statment;

    public function __construct(PDO $pdo, $sql, array $parameters, array $definition)
    {
        $this->pdo        = $pdo;
        $this->sql        = $sql;
        $this->parameters = $parameters;
        $this->definition = $definition;
    }

    public function getDefinition()
    {
        return $this->definition;
    }

    protected function getStatment(array $context = null)
    {
        if ($this->statment === null) {
            $this->statment = $this->pdo->prepare($this->sql);
        }

        $this->statment->execute(ParameterResolver::resolve($this->parameters, $context));

        return $this->statment;
    }
}
