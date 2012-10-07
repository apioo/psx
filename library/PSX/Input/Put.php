<?php
/*
 *  $Id: Put.php 625 2012-08-25 11:19:10Z k42b3.x@googlemail.com $
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

/**
 * A class to get the value of an PUT request. Here a short example how to get
 * values. This is only useful if the PUT request has the format of an
 * application/x-www-form-urlencoded POST request because the content is parsed
 * with the parse_str() function.
 * <code>
 * $put = new PSX_Input_Put();
 *
 * $id = $put->id('integer');
 * </code>
 *
 * If the $_PUT variable id is available it contains an integer representation
 * else of it else false
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Input
 * @version    $Revision: 625 $
 */
class PSX_Input_Put extends PSX_Input
{
	/**
	 * We look whether we have receive a real PUT request or a fake via
	 * HTTP_X_HTTP_METHOD_OVERRIDE. If we have a fake request the values are in
	 * the $_POST data else we look in the php://input stream
	 *
	 * @return void
	 */
	public function __construct(PSX_Validate $validate = null)
	{
		$GLOBALS['_PUT'] = array();

		if(PSX_Base::isOverride() === false)
		{
			$content = PSX_Base::getRawInput();

			if(!empty($content))
			{
				parse_str($content, $GLOBALS['_PUT']);
			}
		}
		else
		{
			if(!empty($_POST))
			{
				$GLOBALS['_PUT'] = $_POST;
			}
		}

		parent::__construct($GLOBALS['_PUT'], $validate);
	}
}

