<?php
/*
 *  $Id: ResultSet.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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
 * PSX_Data_ResultSet
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Data
 * @version    $Revision: 480 $
 */
class PSX_Data_ResultSet extends PSX_Data_RecordAbstract implements Iterator, Countable
{
	public $totalResults;
	public $startIndex;
	public $itemsPerPage;
	public $entry;

	private $pointer;

	public function __construct($totalResults = null, $startIndex = null, $itemsPerPage = null, array $entry = array())
	{
		$this->setTotalResults($totalResults);
		$this->setStartIndex($startIndex);
		$this->setItemsPerPage($itemsPerPage);
		$this->setEntry($entry);
	}

	public function getName()
	{
		return 'resultset';
	}

	public function getFields()
	{
		return array(

			'totalResults' => $this->totalResults,
			'startIndex'   => $this->startIndex,
			'itemsPerPage' => $this->itemsPerPage,
			'entry'        => $this->entry,

		);
	}

	public function setTotalResults($totalResults)
	{
		$this->totalResults = $totalResults;
	}

	public function setStartIndex($startIndex)
	{
		$this->startIndex = $startIndex;
	}

	public function setItemsPerPage($itemsPerPage)
	{
		$this->itemsPerPage = $itemsPerPage;
	}

	public function setEntry(array $entry)
	{
		$this->entry = $entry;
	}

	public function hasResults()
	{
		return count($this->entry) > 0;
	}

	public function getLength()
	{
		return count($this->entry);
	}

	// Iterator
	public function current()
	{
		return $this->pointer = current($this->entry);
	}

	public function key()
	{
		return key($this->entry);
	}

	public function next()
	{
		return $this->pointer = next($this->entry);
	}

	public function rewind()
	{
		$this->pointer = reset($this->entry);
	}

	public function valid()
	{
		return $this->pointer;
	}

	// Countable
	public function count()
	{
		return count($this->entry);
	}
}
