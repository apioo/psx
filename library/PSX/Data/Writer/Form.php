<?php
/*
 *  $Id: Form.php 453 2012-03-12 21:10:02Z k42b3.x@googlemail.com $
 *
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2009 Christoph Kappestein <k42b3.x@gmail.com>
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
 * PSX_Data_Writer_Form
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Data
 * @version    $Revision: 453 $
 */
class PSX_Data_Writer_Form implements PSX_Data_WriterInterface
{
	public static $mime = 'application/x-www-form-urlencoded';

	public $writerResult;

	public function write(PSX_Data_RecordInterface $record)
	{
		$this->writerResult = new PSX_Data_WriterResult(PSX_Data_WriterInterface::FORM, $this);

		echo http_build_query($record->export($this->writerResult), '', '&');
	}
}

