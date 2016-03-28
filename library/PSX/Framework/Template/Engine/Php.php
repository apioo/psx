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

namespace PSX\Framework\Template\Engine;

use PSX\Framework\Template\ErrorException;

/**
 * Php
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Php extends EngineAbstract
{
    public function transform()
    {
        $file = $this->getFile();

        // parse template
        try {
            ob_start();

            includeTemplateScope($this->data, $file);

            $html = ob_get_clean();
        } catch (\Exception $e) {
            throw new ErrorException($e->getMessage(), $e, $file, ob_get_clean());
        }

        return $html;
    }
}

/**
 * Includes the file without exposing the properties of the template object
 *
 * @param array $data
 * @param string $file
 */
function includeTemplateScope(array $data, $file)
{
    // populate the data vars in the scope of the template
    extract($data, EXTR_SKIP);

    // include file
    require_once($file);
}
