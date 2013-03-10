<?php
/*
 *  $Id: ReaderInterface.php 512 2012-06-07 15:03:09Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2012 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Data;

use PSX\Http\Message;

/**
 * PSX_Data_ReaderInterface
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Data
 * @version    $Revision: 512 $
 */
interface ReaderInterface
{
	const DOM       = 0x1;
	const FORM      = 0x2;
	const GPC       = 0x4;
	const JSON      = 0x8;
	const MULTIPART = 0x10;
	const RAW       = 0x20;
	const XML       = 0x40;

	/**
	 * Transforms the $request into an parseable form this can be an array
	 * or DomDocument etc. This method returns an PSX_Data_ReaderResult.
	 * The import method of each record can be used to insert insert the
	 * results.
	 *
	 * @param PSX_Http_Request $request
	 * @return PSX_Data_ReaderResult
	 */
	public function read(Message $message);
}


