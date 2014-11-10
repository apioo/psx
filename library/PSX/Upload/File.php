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

namespace PSX\Upload;

use PSX\File as FileObject;

/**
 * File
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class File
{
	private $name;
	private $type;
	private $size;
	private $tmpName;
	private $error;

	public function __construct(array $file = null)
	{
		if($file !== null)
		{
			$this->setFile($file);
		}
	}

	public function setFile(array $file)
	{
		if($this->isValidUpload($file))
		{
			$this->name    = isset($file['name'])     ? $file['name']     : null;
			$this->type    = isset($file['type'])     ? $file['type']     : null;
			$this->size    = isset($file['size'])     ? $file['size']     : null;
			$this->tmpName = isset($file['tmp_name']) ? $file['tmp_name'] : null;
			$this->error   = isset($file['error'])    ? $file['error']    : null;
		}
		else
		{
			throw new Exception('File was not uploaded');
		}
	}

	public function getName()
	{
		return $this->name;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getSize()
	{
		return $this->size;
	}

	public function getTmpName()
	{
		return $this->tmpName;
	}

	public function getError()
	{
		return $this->error;
	}

	public function move($path)
	{
		return $this->moveUploadedFile($this->tmpName, $path);
	}

	protected function isValidUpload(array $file)
	{
		$error = isset($file['error']) ? $file['error'] : UPLOAD_ERR_NO_FILE;

		switch($error)
		{
			case UPLOAD_ERR_OK:
				return $this->isUploadedFile($file['tmp_name']);
				break;

			case UPLOAD_ERR_INI_SIZE:
				throw new Exception('The uploaded file exceeds the upload_max_filesize directive in php.ini');
				break;

			case UPLOAD_ERR_FORM_SIZE:
				throw new Exception('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form');
				break;

			case UPLOAD_ERR_PARTIAL:
				throw new Exception('The uploaded file was only partially uploaded');
				break;

			case UPLOAD_ERR_NO_FILE:
				throw new Exception('No file was uploaded');
				break;

			case UPLOAD_ERR_NO_TMP_DIR:
				throw new Exception('Missing a temporary folder');
				break;

			case UPLOAD_ERR_CANT_WRITE:
				throw new Exception('Failed to write file to disk');
				break;

			case UPLOAD_ERR_EXTENSION:
				throw new Exception('A PHP extension stopped the file upload');
				break;

			default:
				throw new Exception('Invalid error code');
				break;
		}
	}

	protected function isUploadedFile($tmpName)
	{
		return is_uploaded_file($tmpName);
	}

	protected function moveUploadedFile($tmpName, $path)
	{
		return move_uploaded_file($tmpName, $path);
	}
}

