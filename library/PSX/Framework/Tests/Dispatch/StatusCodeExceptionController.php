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

namespace PSX\Framework\Tests\Dispatch;

use PSX\Framework\Controller\ControllerAbstract;
use PSX\Http\Exception as StatusCode;

/**
 * StatusCodeExceptionController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class StatusCodeExceptionController extends ControllerAbstract
{
    public function throwBadRequestException()
    {
        throw new StatusCode\BadRequestException('foobar');
    }

    public function throwConflictException()
    {
        throw new StatusCode\ConflictException('foobar');
    }

    public function throwForbiddenException()
    {
        throw new StatusCode\ForbiddenException('foobar');
    }

    public function throwFoundException()
    {
        throw new StatusCode\FoundException('http://google.com');
    }

    public function throwGoneException()
    {
        throw new StatusCode\GoneException('foobar');
    }

    public function throwInternalServerErrorException()
    {
        throw new StatusCode\InternalServerErrorException('foobar');
    }

    public function throwMethodNotAllowedException()
    {
        throw new StatusCode\MethodNotAllowedException('foobar', array('GET', 'POST'));
    }

    public function throwMovedPermanentlyException()
    {
        throw new StatusCode\MovedPermanentlyException('http://google.com');
    }

    public function throwNotAcceptableException()
    {
        throw new StatusCode\NotAcceptableException('foobar');
    }

    public function throwNotFoundException()
    {
        throw new StatusCode\NotFoundException('foobar');
    }

    public function throwNotImplementedException()
    {
        throw new StatusCode\NotImplementedException('foobar');
    }

    public function throwNotModifiedException()
    {
        throw new StatusCode\NotModifiedException();
    }

    public function throwSeeOtherException()
    {
        throw new StatusCode\SeeOtherException('http://google.com');
    }

    public function throwServiceUnavailableException()
    {
        throw new StatusCode\ServiceUnavailableException('foobar');
    }

    public function throwTemporaryRedirectException()
    {
        throw new StatusCode\TemporaryRedirectException('http://google.com');
    }

    public function throwUnauthorizedException()
    {
        throw new StatusCode\UnauthorizedException('foobar', 'Basic', array('realm' => 'foo'));
    }

    public function throwUnauthorizedNoParameterException()
    {
        throw new StatusCode\UnauthorizedException('foobar', 'Foo');
    }

    public function throwUnsupportedMediaTypeException()
    {
        throw new StatusCode\UnsupportedMediaTypeException('foobar');
    }
}
