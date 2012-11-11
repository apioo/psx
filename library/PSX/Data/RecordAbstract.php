<?php
/*
 *  $Id: RecordAbstract.php 614 2012-08-25 11:15:07Z k42b3.x@googlemail.com $
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
 * PSX_Data_RecordAbstract
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Data
 * @version    $Revision: 614 $
 */
abstract class PSX_Data_RecordAbstract implements PSX_Data_RecordInterface, Serializable
{
	public function hasFields()
	{
		$fields  = $this->getFields();
		$columns = func_get_args();

		foreach($columns as $column)
		{
			if(!isset($fields[$column]))
			{
				return false;
			}
		}

		return true;
	}

	public function getData()
	{
		$data   = array();
		$fields = $this->getFields();

		foreach($fields as $k => $v)
		{
			if(isset($v))
			{
				$data[$k] = $v;
			}
		}

		return $data;
	}

	public function import(PSX_Data_ReaderResult $result)
	{
		switch($result->getType())
		{
			case PSX_Data_ReaderInterface::FORM:
			case PSX_Data_ReaderInterface::GPC:
			case PSX_Data_ReaderInterface::JSON:
			case PSX_Data_ReaderInterface::MULTIPART:
			case PSX_Data_ReaderInterface::XML:

				$data = (array) $result->getData();
				$data = array_intersect_key($data, $this->getFields());

				foreach($data as $k => $v)
				{
					if(isset($v))
					{
						// convert to camelcase if underscore is in name
						if(strpos($k, '_') !== false)
						{
							$k = implode('', array_map('ucfirst', explode('_', $k)));
						}

						$method = 'set' . ucfirst($k);

						if(is_callable(array($this, $method)))
						{
							$this->$method($v);
						}
					}
				}

				break;

			default:

				throw new PSX_Data_Exception('Reader is not supported');
				break;
		}
	}

	public function export(PSX_Data_WriterResult $result)
	{
		switch($result->getType())
		{
			case PSX_Data_WriterInterface::FORM:
			case PSX_Data_WriterInterface::JSON:
			case PSX_Data_WriterInterface::XML:

				return $this->getData();
				break;

			default:

				throw new PSX_Data_Exception('Writer is not supported');
				break;
		}
	}

	public function serialize()
	{
		return serialize($this->getFields());
	}

	public function unserialize($data)
	{
		$data   = unserialize($data);
		$fields = $this->getFields();

		foreach($fields as $k => $v)
		{
			if(isset($data[$k]))
			{
				$this->$k = $data[$k];
			}
		}
	}
}

