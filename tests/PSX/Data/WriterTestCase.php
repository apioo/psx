<?php
/*
 *  $Id: WriterTestCase.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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
 * PSX_Data_WriterTestCase
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   tests
 * @version    $Revision: 480 $
 */
abstract class PSX_Data_WriterTestCase extends PHPUnit_Framework_TestCase
{
	public function getRecord()
	{
		$record          = new PSX_Data_WriterTestRecord();
		$record->id      = 1;
		$record->author  = 'foo';
		$record->title   = 'bar';
		$record->content = 'foobar';
		$record->date    = '2012-03-11 13:37:21';

		return $record;
	}

	public function getResultSet()
	{
		$entries = array();

		$record          = new PSX_Data_WriterTestRecord();
		$record->id      = 1;
		$record->author  = 'foo';
		$record->title   = 'bar';
		$record->content = 'foobar';
		$record->date    = '2012-03-11 13:37:21';

		$entries[] = $record;

		$record          = new PSX_Data_WriterTestRecord();
		$record->id      = 2;
		$record->author  = 'foo';
		$record->title   = 'bar';
		$record->content = 'foobar';
		$record->date    = '2012-03-11 13:37:21';

		$entries[] = $record;

		return new PSX_Data_ResultSet(2, 0, 8, $entries);
	}

	public function getComplexRecord()
	{
		$plural = new PSX_OpenSocial_Type_Plural();

		$acount = new PSX_OpenSocial_Type_Account();
		$acount->domain = 'foo.com';
		$acount->username = 'foo';
		$acount->userId = 1;

		$plural->add($acount, 'home', true);

		$acount = new PSX_OpenSocial_Type_Account();
		$acount->domain = 'bar.com';
		$acount->username = 'foo';
		$acount->userId = 1;

		$plural->add($acount, 'work', false);

		$author = new PSX_OpenSocial_Type_Person();
		$author->id = 1;
		$author->displayName = 'foobar';
		$author->accounts = $plural;

		$article = new PSX_ActivityStream_Type_Article();
		$article->author = $author;
		$article->displayName = 'content';
		$article->id = 1;

		return $article;
	}

	abstract public function testWrite();
	abstract public function testWriteResultSet();
}

