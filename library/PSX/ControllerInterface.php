<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX;

/**
 * ControllerInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface ControllerInterface
{
	const CALL_REQUEST_FILTER   = 0x1;
	const CALL_ONLOAD           = 0x2;
	const CALL_REQUEST_METHOD   = 0x4;
	const CALL_METHOD           = 0x8;
	const CALL_PROCESS_RESPONSE = 0x10;
	const CALL_RESPONSE_FILTER  = 0x20;

	/**
	 * The controller can control the behaviour wich method should be called by 
	 * the loader. Returns an integer of OR connected CALL_* constants. In most 
	 * cases you do not need to modify this behaviour
	 *
	 * @return integer
	 */
	public function getStage();

	/**
	 * Returns an array of request filters wich are applied on the current 
	 * request
	 *
	 * @return array<PSX\Dispatch\RequestFilterInterface>
	 */
	public function getRequestFilter();

	/**
	 * Returns an array of response filters wich are applied on the response
	 *
	 * @return array<PSX\Dispatch\ResponseFilterInterface>
	 */
	public function getResponseFilter();

	/**
	 * Method which gets always called before the on* methods are called
	 */
	public function onLoad();

	/**
	 * Method which gets called on an GET request
	 */
	public function onGet();

	/**
	 * Method which gets called on an POST request
	 */
	public function onPost();

	/**
	 * Method which gets called on an PUT request
	 */
	public function onPut();

	/**
	 * Method which gets called on an DELETE request
	 */
	public function onDelete();

	/**
	 * The controller should write any produced content to the response body
	 *
	 * @param string $content
	 * @return string
	 */
	public function processResponse();
}
