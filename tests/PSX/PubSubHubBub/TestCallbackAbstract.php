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

namespace PSX\PubSubHubBub;

use PSX\Atom;
use PSX\Rss;
use PSX\Url;

/**
 * TestCallbackAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class TestCallbackAbstract extends CallbackAbstract
{
	protected function onAtom(Atom $atom)
	{
		$entry = $atom->current();

		$this->getTestCase()->assertEquals('Atom draft-07 snapshot', $entry->getTitle());
		$this->getTestCase()->assertEquals('tag:example.org,2003:3.2397', $entry->getId());
		$this->getTestCase()->assertEquals('2003-12-13 08:29:29', $entry->getPublished()->format('Y-m-d H:i:s'));
		$this->getTestCase()->assertEquals('foobar', $entry->getContent());
	}

	protected function onRss(Rss $rss)
	{
		$item = $rss->current();

		$this->getTestCase()->assertEquals('Star City', $item->getTitle());
		$this->getTestCase()->assertEquals('http://liftoff.msfc.nasa.gov/2003/06/03.html#item573', $item->getGuid());
		$this->getTestCase()->assertEquals('2003-06-03 09:39:21', $item->getPubDate()->format('Y-m-d H:i:s'));
		$this->getTestCase()->assertEquals('foobar', $item->getDescription());
	}

	protected function onVerify($mode, Url $topic, $leaseSeconds)
	{
		$this->getTestCase()->assertEquals('subscribe', $mode);
		$this->getTestCase()->assertEquals('http://127.0.0.1/topic', (string) $topic);

		return true;
	}
}
