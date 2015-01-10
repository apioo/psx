<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This file is part of psx. psx is free software: you can
 * redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or any later version.
 *
 * psx is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with psx. If not, see <http://www.gnu.org/licenses/>.
 */

namespace PSX\Dispatch;

use PSX\ControllerAbstract;
use PSX\Http\Exception as StatusCode;

/**
 * StatusCodeExceptionController
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
