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

namespace PSX\Data\Record\Visitor;

use PSX\Data\Record\VisitorAbstract;
use PSX\DateTime;

/**
 * HtmlWriterVisitor
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class HtmlWriterVisitor extends VisitorAbstract
{
    protected $output;

    public function __construct()
    {
        $this->output = '';
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function visitObjectStart($name)
    {
        $this->write('<dl data-name="' . htmlspecialchars($name) . '">');
    }

    public function visitObjectEnd()
    {
        $this->write('</dl>');
    }

    public function visitObjectValueStart($key, $value)
    {
        $this->write('<dt>' . htmlspecialchars($key) . '</dt><dd>');
    }

    public function visitObjectValueEnd()
    {
        $this->write('</dd>');
    }

    public function visitArrayStart()
    {
        $this->write('<ul>');
    }

    public function visitArrayEnd()
    {
        $this->write('</ul>');
    }

    public function visitArrayValueStart($value)
    {
        $this->write('<li>');
    }

    public function visitArrayValueEnd()
    {
        $this->write('</li>');
    }

    public function visitValue($value)
    {
        $this->write($this->getValue($value));
    }

    protected function write($message)
    {
        $this->output.= $message;
    }

    protected function getValue($value)
    {
        if ($value instanceof \DateTime) {
            return DateTime::getFormat($value);
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } else {
            return htmlspecialchars((string) $value);
        }
    }
}
