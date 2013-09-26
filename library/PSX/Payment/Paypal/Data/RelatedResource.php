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

namespace PSX\Payment\Paypal\Data;

use ReflectionClass;
use PSX\Data\RecordAbstract;
use PSX\Data\ReaderResult;
use PSX\Data\ReaderInterface;
use PSX\Url;
use PSX\Payment\Paypal\Data\Sale;
use PSX\Payment\Paypal\Data\Refund;

/**
 * RelatedResource
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RelatedResource extends RecordAbstract
{
	protected $resources = array();

	public function getName()
	{
		return 'related_resources';
	}

	public function getFields()
	{
		return array(
			'resources' => $this->resources,
		);
	}

	public function getSale()
	{
		foreach($this->resources as $resource)
		{
			if($resource instanceof Sale)
			{
				return $resource;
			}
		}
		return null;
	}

	public function getRefund()
	{
		foreach($this->resources as $resource)
		{
			if($resource instanceof Refund)
			{
				return $resource;
			}
		}
		return null;
	}

	public function import(ReaderResult $result)
	{
		$class = new ReflectionClass($this);

		switch($result->getType())
		{
			case ReaderInterface::JSON:

				$data = (array) $result->getData();

				foreach($data as $v)
				{
					if(isset($v))
					{
						$key   = key($v);
						$value = current($v);
						$class = 'PSX\\Payment\\Paypal\\Data\\' . ucfirst(strtolower($key));

						if(class_exists($class))
						{
							$result   = new ReaderResult($result->getType(), $value);
							$resource = new $class();
							$resource->import($result);

							$this->resources[] = $resource;
						}
					}
				}
				break;

			default:
				parent::import($result);
				break;
		}
	}
}
