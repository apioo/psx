<?php
/*
 *  $Id: Request.php 626 2012-08-25 11:19:36Z k42b3.x@googlemail.com $
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
 * A class to get the $_REQUEST variables. Here a short example how to get
 * values.
 * <code>
 * $request = new PSX_Input_Request();
 *
 * $id = $request->id('integer');
 * </code>
 *
 * If the $_REQUEST variable id is available it contains an integer
 * representation else of it else false
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Input
 * @version    $Revision: 626 $
 */
class PSX_Input_Request extends PSX_Input
{
	public function __construct(PSX_Validate $validate = null)
	{
		parent::__construct($_REQUEST, $validate);
	}
}

