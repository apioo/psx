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

namespace PSX\Hostmeta;

use DateTime;

/**
 * Document
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Jrd extends DocumentAbstract
{
	public function import(array $data)
	{
		if(isset($data['subject']))
		{
			$this->setSubject($data['subject']);
		}

		if(isset($data['expires']))
		{
			$this->setExpires(new DateTime($data['expires']));
		}

		if(isset($data['aliases']))
		{
			$this->setAliases($data['aliases']);
		}

		if(isset($data['properties']))
		{
			$this->setProperties($data['properties']);
		}

		if(isset($data['links']) && is_array($data['links']))
		{
			foreach($data['links'] as $link)
			{
				$this->addLink($this->parseLink($link));
			}
		}
	}

	public function export()
	{
		$data = array();

		if(!empty($this->subject))
		{
			$data['subject'] = $this->subject;
		}

		if(!empty($this->expires))
		{
			$data['expires'] = $this->expires->format(DateTime::RFC3339);
		}

		if(!empty($this->aliases))
		{
			$data['aliases'] = $this->aliases;
		}

		if(!empty($this->properties))
		{
			$data['properties'] = $this->properties;
		}

		if(!empty($this->links))
		{
			$data['links'] = array();

			foreach($this->links as $link)
			{
				$data['links'][] = $this->buildLink($link);
			}
		}

		return json_encode($data);
	}

	protected function parseLink(array $row)
	{
		$link = new Link();

		if(isset($row['rel']))
		{
			$link->setRel($row['rel']);
		}
		else
		{
			throw new Exception('Rel member must be present');
		}

		if(isset($row['type']))
		{
			$link->setType($row['type']);
		}

		if(isset($row['href']))
		{
			$link->setHref($row['href']);
		}
		else if(isset($row['template']))
		{
			$link->setTemplate($row['template']);
		}

		if(isset($row['titles']))
		{
			$link->setTitles($row['titles']);
		}

		if(isset($row['properties']))
		{
			$link->setProperties($row['properties']);
		}

		return $link;
	}

	protected function buildLink(Link $link)
	{
		$row = array('rel' => $link->getRel());

		$type = $link->getType();
		if(!empty($type))
		{
			$row['type'] = $type;
		}

		$href = $link->getHref();
		if(!empty($href))
		{
			$row['href'] = $href;
		}

		$template = $link->getTemplate();
		if(!empty($template))
		{
			$row['template'] = $template;
		}

		$titles = $link->getTitles();
		if(!empty($titles))
		{
			$row['titles'] = $titles;
		}

		$properties = $link->getProperties();
		if(!empty($properties))
		{
			$row['properties'] = $properties;
		}

		return $row;
	}
}
