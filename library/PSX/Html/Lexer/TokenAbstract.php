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

namespace PSX\Html\Lexer;

/**
 * TokenAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
abstract class TokenAbstract
{
	const TYPE_ELEMENT_START = 0x1; // start tag <b>
	const TYPE_ELEMENT_END   = 0x2; // end tag </b>
	const TYPE_TEXT          = 0x3;
	const TYPE_COMMENT       = 0x4;

	protected $type;
	protected $name;
	protected $parentNode;
	protected $childNodes = array();

	public function getType()
	{
		return $this->type;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setParentNode(TokenAbstract $parentNode)
	{
		$this->parentNode = $parentNode;
	}

	public function getParentNode()
	{
		return $this->parentNode;
	}

	public function appendChild(TokenAbstract $token)
	{
		if($token->getParentNode() !== null)
		{
			throw new DomException('Token is already appended to an element');
		}
		else
		{
			$token->setParentNode($this);
		}

		$this->childNodes[] = $token;
	}

	public function setChild($key, TokenAbstract $token)
	{
		$this->childNodes[$key] = $token;
	}

	public function removeChild($key)
	{
		if(isset($this->childNodes[$key]))
		{
			unset($this->childNodes[$key]);
		}
	}

	public function getChildNodes()
	{
		return $this->childNodes;
	}

	public function hasChildNodes()
	{
		return count($this->childNodes) > 0;
	}

	abstract public function __toString();
}

