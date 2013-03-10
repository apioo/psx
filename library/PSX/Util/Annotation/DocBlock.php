<?php
/*
 *  $Id: Exception.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

namespace PSX\Util\Annotation;

/**
 * PSX_Util_Annotation_DocBlock
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Util
 * @version    $Revision: 480 $
 */
class DocBlock
{
	private $annotations = array();
	private $text;

	/**
	 * Adds an annotation
	 *
	 * @param string $key
	 * @param string $value
	 * @return void
	 */
	public function addAnnotation($key, $value)
	{
		if(!isset($this->annotations[$key]))
		{
			$this->annotations[$key] = array();
		}

		$this->annotations[$key][] = $value;
	}

	/**
	 * Returns all annotations associated with the $key
	 *
	 * @param string $key
	 * @return array
	 */
	public function getAnnotation($key)
	{
		if(isset($this->annotations[$key]))
		{
			return $this->annotations[$key];
		}
		else
		{
			return array();
		}
	}

	public function hasAnnotation()
	{
		return isset($this->annotations[$key]);
	}

	public function removeAnnotation($key)
	{
		unset($this->annotations[$key]);
	}

	public function getAnnotations()
	{
		return $this->annotations;
	}

	public function setAnnotations($key, array $values)
	{
		$this->annotations[$key] = $values;
	}

	/**
	 * Returns te first annotation for the $key
	 *
	 * @param string $key
	 * @return string|null
	 */
	public function getFirstAnnotation($key)
	{
		$annotation = $this->getAnnotation($key);

		return isset($annotation[0]) ? $annotation[0] : null;
	}

	/**
	 * Sets the docblock body text that means all content wich is not an
	 * annotation (starts with @..)
	 *
	 * @param string $text
	 * @return void
	 */
	public function setText($text)
	{
		$this->text = $text;
	}

	/**
	 * Returns the text
	 *
	 * @return string
	 */
	public function getText()
	{
		return $this->text;
	}

	public function __toString()
	{
		return $this->getText();
	}
}
