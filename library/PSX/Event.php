<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2014 Christoph Kappestein <k42b3.x@gmail.com>
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
 * Event
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Event
{
	const COMMAND_EXECUTE      = 'psx.command_execute';
	const COMMAND_PROCESSED    = 'psx.command_processed';
	const CONTROLLER_EXECUTE   = 'psx.controller_execute';
	const CONTROLLER_PROCESSED = 'psx.controller_processed';
	const EXCEPTION_THROWN     = 'psx.exception_thrown';
	const REQUEST_INCOMING     = 'psx.request_incoming';
	const RESPONSE_SEND        = 'psx.response_send';
	const ROUTE_MATCHED        = 'psx.route_matched';
}
