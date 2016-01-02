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

namespace PSX\Data;

/**
 * ExceptionRecord
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ExceptionRecord extends RecordAbstract
{
    protected $success;
    protected $title;
    protected $message;
    protected $trace;
    protected $context;

    public function getRecordInfo()
    {
        return new RecordInfo('error', [
            'success' => $this->success,
            'title'   => $this->title,
            'message' => $this->message,
            'trace'   => $this->trace,
            'context' => $this->context,
        ]);
    }

    public function setSuccess($success)
    {
        $this->success = $success;
    }
    
    public function getSuccess()
    {
        return $this->success;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }
    
    public function getTitle()
    {
        return $this->title;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }
    
    public function getMessage()
    {
        return $this->message;
    }

    public function setTrace($trace)
    {
        $this->trace = $trace;
    }
    
    public function getTrace()
    {
        return $this->trace;
    }

    public function setContext($context)
    {
        $this->context = $context;
    }
    
    public function getContext()
    {
        return $this->context;
    }
}
