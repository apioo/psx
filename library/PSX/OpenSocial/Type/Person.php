<?php
/*
 *  $Id: Person.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

namespace PSX\OpenSocial\Type;

use PSX\OpenSocial\TypeAbstract;

/**
 * PSX_OpenSocial_Type_Person
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_OpenSocial
 * @version    $Revision: 480 $
 */
class Person extends TypeAbstract
{
	public $aboutMe;
	public $accounts;
	public $addresses;
	public $alternateNames;
	public $appData;
	public $connected;
	public $contactPreference;
	public $dn;
	public $displayName;
	public $emails;
	public $hasApp;
	public $id;
	public $ims;
	public $location;
	public $name;
	public $nativeName;
	public $networkPresence;
	public $organizations;
	public $phoneNumbers;
	public $photos;
	public $preferredName;
	public $preferredUsername;
	public $profileUrl;
	public $published;
	public $relationships;
	public $status;
	public $tags;
	public $thumbnailUrl;
	public $updated;
	public $urls;
	public $utcOffset;

	public function getName()
	{
		return 'person';
	}

	public function getFields()
	{
		return array(

			'aboutMe'           => $this->aboutMe,
			'accounts'          => $this->accounts,
			'addresses'         => $this->addresses,
			'alternateNames'    => $this->alternateNames,
			'appData'           => $this->appData,
			'connected'         => $this->connected,
			'contactPreference' => $this->contactPreference,
			'dn'                => $this->dn,
			'displayName'       => $this->displayName,
			'emails'            => $this->emails,
			'hasApp'            => $this->hasApp,
			'id'                => $this->id,
			'ims'               => $this->ims,
			'location'          => $this->location,
			'name'              => $this->name,
			'nativeName'        => $this->nativeName,
			'networkPresence'   => $this->networkPresence,
			'organizations'     => $this->organizations,
			'phoneNumbers'      => $this->phoneNumbers,
			'photos'            => $this->photos,
			'preferredName'     => $this->preferredName,
			'preferredUsername' => $this->preferredUsername,
			'profileUrl'        => $this->profileUrl,
			'published'         => $this->published,
			'relationships'     => $this->relationships,
			'status'            => $this->status,
			'tags'              => $this->tags,
			'thumbnailUrl'      => $this->thumbnailUrl,
			'updated'           => $this->updated,
			'urls'              => $this->urls,
			'utcOffset'         => $this->utcOffset,

		);
	}
}

