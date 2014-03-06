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

namespace PSX\Handler\Doctrine;

use Doctrine\ORM\Internal\Hydration\AbstractHydrator;
use PDO;
use PSX\Data\Record;
use RuntimeException;

/**
 * Hydrator wich create records
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class RecordHydrator extends AbstractHydrator
{
	const HYDRATE_RECORD = 'RecordHydrator';

	private $_idTemplate;

	public function prepare()
	{
		$this->_idTemplate = array();

		foreach($this->_rsm->aliasMap as $dqlAlias => $className)
		{
			$this->_idTemplate[$dqlAlias] = '';
		}
	}

	protected function hydrateAllData()
	{
		$result = array();
		$cache  = array();

		while($data = $this->_stmt->fetch(PDO::FETCH_ASSOC))
		{
			$this->hydrateRowData($data, $cache, $result);
		}

		return $result;
	}

	protected function hydrateRowData(array $row, array &$cache, array &$result)
	{
		$id = $this->_idTemplate; // initialize the id-memory
		$nonemptyComponents = array();
		$rowData = $this->gatherRowData($row, $cache, $id, $nonemptyComponents);

		if(isset($rowData['scalars']))
		{
			$result[] = new Record('record', $rowData['scalars']);
		}
		else
		{
			$data = array();
			$ids  = array_keys($id);

			foreach($ids as $k => $key)
			{
				if($k == 0)
				{
					$data = $rowData[$key];
				}
				else
				{
					$func = function($k) use ($key){
						return $key . ucfirst($k);
					};

					$keys   = array_map($func, array_keys($rowData[$key]));
					$values = array_values($rowData[$key]);
					$data   = array_merge($data, array_combine($keys, $values));
				}
			}

			$result[] = new Record('record', $data);
		}
	}
}
