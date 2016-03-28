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

namespace PSX\Framework\Controller;

use PSX\Framework\Loader\Context;

/**
 * ErrorController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ErrorController extends ViewAbstract
{
    /**
     * @Inject
     * @var \PSX\Framework\Exception\Converter
     */
    protected $exceptionConverter;

    public function processResponse()
    {
        $exception = $this->context->get(Context::KEY_EXCEPTION);

        if ($exception instanceof \Exception) {
            $this->handleException($exception);
        }
    }

    protected function handleException(\Exception $exception)
    {
        // set error template
        $class = str_replace('\\', '/', get_class($this));

        if (strpos($class, '/Application/') !== false) {
            $path = PSX_PATH_LIBRARY . '/' . strstr($class, '/Application/', true) . '/Resource';
            $file = substr(strstr($class, 'Application'), 12);
            $file = $this->underscore($file) . '.html';

            if (!is_file($path . '/' . $file)) {
                $this->template->set($this->getFallbackTemplate());
            }
        } else {
            $this->template->set($this->getFallbackTemplate());
        }

        // build message
        $record = $this->exceptionConverter->convert($exception);

        $this->setBody($record);
    }

    /**
     * Returns the fallback template which is used if the template has no file
     * and the controller is not in an application structure
     *
     * @return string|\Closure
     */
    protected function getFallbackTemplate()
    {
        if (isset($this->config['psx_error_template'])) {
            return $this->config['psx_error_template'];
        } else {
            return __DIR__ . '/Resource/error_controller.html';
        }
    }

    protected function underscore($word)
    {
        return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $word));
    }
}
