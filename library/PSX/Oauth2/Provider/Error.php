<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Oauth2\Provider;

use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Error
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Error extends RecordAbstract
{
    protected $error;
    protected $errorDescription;
    protected $errorUri;
    protected $state;

    public function getRecordInfo()
    {
        return new RecordInfo('error', array(
            'error' => $this->error,
            'error_description' => $this->errorDescription,
            'error_uri' => $this->errorUri,
            'state' => $this->state,
        ));
    }

    public function setError($error)
    {
        $this->error = $error;
    }
    
    public function getError()
    {
        return $this->error;
    }

    public function setErrorDescription($errorDescription)
    {
        $this->errorDescription = $errorDescription;
    }
    
    public function getErrorDescription()
    {
        return $this->errorDescription;
    }

    public function setErrorUri($errorUri)
    {
        $this->errorUri = $errorUri;
    }
    
    public function getErrorUri()
    {
        return $this->errorUri;
    }

    public function setState($state)
    {
        $this->state = $state;
    }
    
    public function getState()
    {
        return $this->state;
    }
}
