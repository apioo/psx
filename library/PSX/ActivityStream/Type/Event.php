<?php
/*
 *  $Id: Event.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

namespace PSX\ActivityStream\Type;

use PSX\ActivityStream\TypeAbstract;

/**
 * PSX_ActivityStream_Type_Event
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_ActivityStream
 * @version    $Revision: 480 $
 */
class Event extends TypeAbstract
{
	public $attending;
	public $author;
	public $displayName;
	public $endTime;
	public $id;
	public $location;
	public $maybeAttending;
	public $notAttending;
	public $published;
	public $startTime;
	public $summary;
	public $updated;
	public $url;

	public function getName()
	{
		return 'event';
	}

	public function getFields()
	{
		return array(

			'objectType'     => $this->getName(),
			'attending'      => $this->attending,
			'author'         => $this->author,
			'displayName'    => $this->displayName,
			'endTime'        => $this->endTime,
			'id'             => $this->id,
			'location'       => $this->location,
			'maybeAttending' => $this->maybeAttending,
			'notAttending'   => $this->notAttending,
			'published'      => $this->published,
			'startTime'      => $this->startTime,
			'summary'        => $this->summary,
			'updated'        => $this->updated,
			'url'            => $this->url,

		);
	}
}

