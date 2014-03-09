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

namespace PSX\OpenSocial\Data;

use DateTime;
use PSX\Data\RecordAbstract;
use PSX\Data\RecordInfo;

/**
 * Person
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Person extends RecordAbstract
{
	protected $aboutMe;
	protected $accounts;
	protected $addresses;
	protected $alternateNames;
	protected $appData;
	protected $connected;
	protected $contactPreference;
	protected $dn;
	protected $displayName;
	protected $emails;
	protected $hasApp;
	protected $id;
	protected $ims;
	protected $location;
	protected $name;
	protected $nativeName;
	protected $networkPresence;
	protected $organizations;
	protected $phoneNumbers;
	protected $photos;
	protected $preferredName;
	protected $preferredUsername;
	protected $profileUrl;
	protected $published;
	protected $relationships;
	protected $status;
	protected $tags;
	protected $thumbnailUrl;
	protected $updated;
	protected $urls;
	protected $utcOffset;

	/**
	 * @param string
	 */
	public function setAboutMe($aboutMe)
	{
		$this->aboutMe = $aboutMe;
	}
	
	public function getAboutMe()
	{
		return $this->aboutMe;
	}

	/**
	 * @param array<PSX\OpenSocial\PluralField>
	 */
	public function setAccounts(array $accounts)
	{
		$this->accounts = $accounts;
	}
	
	public function getAccounts()
	{
		return $this->accounts;
	}

	/**
	 * @param array<PSX\OpenSocial\PluralField>
	 */
	public function setAddresses(array $addresses)
	{
		$this->addresses = $addresses;
	}
	
	public function getAddresses()
	{
		return $this->addresses;
	}

	/**
	 * @param array<PSX\OpenSocial\PluralField>
	 */
	public function setAlternateNames(array $alternateNames)
	{
		$this->alternateNames = $alternateNames;
	}
	
	public function getAlternateNames()
	{
		return $this->alternateNames;
	}

	/**
	 * @param array<PSX\OpenSocial\PluralField>
	 */
	public function setAppData(array $appData)
	{
		$this->appData = $appData;
	}
	
	public function getAppData()
	{
		return $this->appData;
	}

	/**
	 * @param boolean
	 */
	public function setConnected($connected)
	{
		$this->connected = $connected;
	}
	
	public function getConnected()
	{
		return $this->connected;
	}

	/**
	 * @param string
	 */
	public function setContactPreference($contactPreference)
	{
		$this->contactPreference = $contactPreference;
	}
	
	public function getContactPreference()
	{
		return $this->contactPreference;
	}

	/**
	 * @param string
	 */
	public function setDn($dn)
	{
		$this->dn = $dn;
	}
	
	public function getDn()
	{
		return $this->dn;
	}

	/**
	 * @param string
	 */
	public function setDisplayName($displayName)
	{
		$this->displayName = $displayName;
	}
	
	public function getDisplayName()
	{
		return $this->displayName;
	}

	/**
	 * @param array<PSX\OpenSocial\PluralField>
	 */
	public function setEmails(array $emails)
	{
		$this->emails = $emails;
	}
	
	public function getEmails()
	{
		return $this->emails;
	}

	/**
	 * @param boolean
	 */
	public function setHasApp($hasApp)
	{
		$this->hasApp = $hasApp;
	}
	
	public function getHasApp()
	{
		return $this->hasApp;
	}

	/**
	 * @param string
	 */
	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param array<PSX\OpenSocial\PluralField>
	 */
	public function setIms(array $ims)
	{
		$this->ims = $ims;
	}
	
	public function getIms()
	{
		return $this->ims;
	}

	/**
	 * @param string
	 */
	public function setLocation($location)
	{
		$this->location = $location;
	}
	
	public function getLocation()
	{
		return $this->location;
	}

	/**
	 * @param PSX\OpenSocial\Data\Name
	 */
	public function setName(Name $name)
	{
		$this->name = $name;
	}
	
	public function getPersonName()
	{
		return $this->name;
	}

	/**
	 * @param PSX\OpenSocial\Data\Name
	 */
	public function setNativeName($nativeName)
	{
		$this->nativeName = $nativeName;
	}
	
	public function getNativeName()
	{
		return $this->nativeName;
	}

	/**
	 * @param array<PSX\OpenSocial\PluralField>
	 */
	public function setNetworkPresence(array $networkPresence)
	{
		$this->networkPresence = $networkPresence;
	}
	
	public function getNetworkPresence()
	{
		return $this->networkPresence;
	}

	/**
	 * @param array<PSX\OpenSocial\PluralField>
	 */
	public function setOrganizations(array $organizations)
	{
		$this->organizations = $organizations;
	}
	
	public function getOrganizations()
	{
		return $this->organizations;
	}

	/**
	 * @param array<PSX\OpenSocial\PluralField>
	 */
	public function setPhoneNumbers(array $phoneNumbers)
	{
		$this->phoneNumbers = $phoneNumbers;
	}
	
	public function getPhoneNumbers()
	{
		return $this->phoneNumbers;
	}

	/**
	 * @param array<PSX\OpenSocial\PluralField>
	 */
	public function setPhotos(array $photos)
	{
		$this->photos = $photos;
	}
	
	public function getPhotos()
	{
		return $this->photos;
	}

	/**
	 * @param PSX\OpenSocial\Data\Name
	 */
	public function setPreferredName(Name $preferredUsername)
	{
		$this->preferredUsername = $preferredUsername;
	}
	
	public function getPreferredName()
	{
		return $this->preferredUsername;
	}

	/**
	 * @param string
	 */
	public function setPreferredUserName($preferredName)
	{
		$this->preferredName = $preferredName;
	}
	
	public function getPreferredUserName()
	{
		return $this->preferredName;
	}

	/**
	 * @param string
	 */
	public function setProfileUrl($profileUrl)
	{
		$this->profileUrl = $profileUrl;
	}
	
	public function getProfileUrl()
	{
		return $this->profileUrl;
	}

	/**
	 * @param DateTime
	 */
	public function setPublished(DateTime $published)
	{
		$this->published = $published;
	}
	
	public function getPublished()
	{
		return $this->published;
	}

	/**
	 * @param array<PSX\OpenSocial\PluralField>
	 */
	public function setRelationships(array $relationships)
	{
		$this->relationships = $relationships;
	}
	
	public function getRelationships()
	{
		return $this->relationships;
	}

	/**
	 * @param string
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}
	
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param array<PSX\OpenSocial\PluralField>
	 */
	public function setTags(array $tags)
	{
		$this->tags = $tags;
	}
	
	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * @param string
	 */
	public function setThumbnailUrl($thumbnailUrl)
	{
		$this->thumbnailUrl = $thumbnailUrl;
	}
	
	public function getThumbnailUrl()
	{
		return $this->thumbnailUrl;
	}

	/**
	 * @param DateTime
	 */
	public function setUpdated(DateTime $updated)
	{
		$this->updated = $updated;
	}
	
	public function getUpdated()
	{
		return $this->updated;
	}

	/**
	 * @param array<PSX\OpenSocial\PluralField>
	 */
	public function setUrls(array $urls)
	{
		$this->urls = $urls;
	}
	
	public function getUrls()
	{
		return $this->urls;
	}

	/**
	 * @param string
	 */
	public function setUtcOffset($utcOffset)
	{
		$this->utcOffset = $utcOffset;
	}
	
	public function getUtcOffset()
	{
		return $this->utcOffset;
	}
}

