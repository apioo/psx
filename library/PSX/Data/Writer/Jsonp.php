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

namespace PSX\Data\Writer;

use PSX\Http\MediaType;

/**
 * Jsonp
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Jsonp extends Json
{
    protected static $mime = 'application/javascript';

    protected $callbackName;

    public function write($data)
    {
        $callbackName = $this->getCallbackName();

        if (!empty($callbackName)) {
            return $callbackName . '(' . parent::write($data) . ')';
        } else {
            return parent::write($data);
        }
    }

    public function isContentTypeSupported(MediaType $contentType)
    {
        return $contentType->getName() == self::$mime;
    }

    public function getContentType()
    {
        return self::$mime;
    }

    public function getCallbackName()
    {
        return $this->callbackName;
    }

    public function setCallbackName($callbackName)
    {
        if (preg_match('/^([A-Za-z0-9._]{3,32})$/', $callbackName)) {
            $this->callbackName = $callbackName;
        }
    }
}
