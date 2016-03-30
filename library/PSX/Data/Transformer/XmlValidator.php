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

namespace PSX\Data\Transformer;

use DOMDocument;
use InvalidArgumentException;
use PSX\Http\MediaType;
use RuntimeException;

/**
 * XmlValidator
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class XmlValidator extends XmlArray
{
    protected $xsdSchema;

    public function __construct($xsdSchema)
    {
        $this->xsdSchema = $xsdSchema;
    }

    public function transform($data)
    {
        if (!$data instanceof DOMDocument) {
            throw new InvalidArgumentException('Data must be an instanceof DOMDocument');
        }

        $useErrors = libxml_use_internal_errors(true);

        $data->schemaValidate($this->xsdSchema);

        $errors = $this->getXmlErrors();

        libxml_use_internal_errors($useErrors);

        if (count($errors) > 0) {
            $this->throwXmlError(current($errors));
        }

        return parent::transform($data);
    }

    protected function getXmlErrors()
    {
        $errors = libxml_get_errors();
        $result = [];

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $result[] = $error;
            }
        }

        libxml_clear_errors();

        return $result;
    }

    protected function throwXmlError(\LibXMLError $error)
    {
        throw new RuntimeException(rtrim($error->message));
    }
}
