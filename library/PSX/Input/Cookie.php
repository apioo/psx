<?php
/*
 *  $Id: Cookie.php 621 2012-08-25 11:18:00Z k42b3.x@googlemail.com $
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

namespace PSX\Input;

use PSX\Input;
use PSX\Validate;

/**
 * A class to set get and delete cookies. Here a short example how to get
 * cookie values.
 * <code>
 * $cookie = new PSX_Input_Cookie();
 *
 * // set the data type
 * $foo = $cookie->foo('integer');
 *
 * // use the validation class
 * $foo = $cookie->foo('integer', new PSX_Filter_Url());
 *
 * // get the raw cookie value
 * $foo = $cookie->foo;
 * </code>
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Input
 * @version    $Revision: 621 $
 */
class Cookie extends Input
{
	public function __construct(Validate $validate = null)
	{
		parent::__construct($_COOKIE, $validate);
	}

	public function offsetSet($offset, $value)
	{
		parent::offsetSet($offset, $value);

		if(!headers_sent())
		{
			setcookie($offset, $value);
		}
	}

	public function offsetUnset($offset)
	{
		$this->offsetSet($offset, null);
	}
}

