<?php
/*
 *  $Id: Files.php 623 2012-08-25 11:18:38Z k42b3.x@googlemail.com $
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
 * A class to get the $_FILES variables from an file upload. Here a short
 * example how to upload a file.
 * <code>
 * $files = new PSX_Input_Files();
 * $file  = $files->userfile(null, array(new PSX_Filter_Length(3, 1024)));
 *
 * if($file !== false && $file->move('/home/foo/upload/' . $file->getName()))
 * {
 *   // file successful uploaded
 * }
 * else
 * {
 *   // an error occured
 * }
 * </code>
 *
 * In this exmaple the file must have a length between 3 and 1024.
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Input
 * @version    $Revision: 623 $
 */
class PSX_Input_Files extends PSX_Input
{
	public function __construct(PSX_Validate $validate = null)
	{
		parent::__construct($_FILES, $validate);
	}

	public function offsetGet($offset)
	{
		return isset($this->container[$offset]) ? new PSX_Upload_File($this->container[$offset]) : false;
	}
}

