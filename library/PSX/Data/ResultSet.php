<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Data;

/**
 * ResultSet
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class ResultSet extends CollectionAbstract
{
	protected $totalResults;
	protected $startIndex;
	protected $itemsPerPage;

	public function __construct($totalResults = null, $startIndex = null, $itemsPerPage = null, array $entries = array())
	{
		parent::__construct($entries);

		$this->setTotalResults($totalResults);
		$this->setStartIndex($startIndex);
		$this->setItemsPerPage($itemsPerPage);
	}

	public function getRecordInfo()
	{
		return new RecordInfo('resultset', array(
			'totalResults' => $this->totalResults,
			'startIndex'   => $this->startIndex,
			'itemsPerPage' => $this->itemsPerPage,
			'entry'        => $this->collection,
		));
	}

	public function getTotalResults()
	{
		return $this->totalResults;
	}

	public function setTotalResults($totalResults)
	{
		$this->totalResults = $totalResults;
	}

	public function getStartIndex()
	{
		return $this->startIndex;
	}

	public function setStartIndex($startIndex)
	{
		$this->startIndex = $startIndex;
	}

	public function getItemsPerPage()
	{
		return $this->itemsPerPage;
	}

	public function setItemsPerPage($itemsPerPage)
	{
		$this->itemsPerPage = $itemsPerPage;
	}
}
