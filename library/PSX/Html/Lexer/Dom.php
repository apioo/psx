<?php
/*
 *  $Id: Dom.php 541 2012-07-10 19:41:07Z k42b3.x@googlemail.com $
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
 * PSX_Html_Lexer_Dom
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Html
 * @version    $Revision: 541 $
 */
class PSX_Html_Lexer_Dom
{
	private $root;
	private $stack = array();

	public function push(PSX_Html_Lexer_TokenAbstract $token)
	{
		$topToken = $this->getTopToken();

		if($topToken !== false)
		{
			if($token instanceof PSX_Html_Lexer_Token_Element)
			{
				if($token->type == PSX_Html_Lexer_Token_Element::TYPE_START)
				{
					array_push($this->stack, $token);

					$topToken->appendChild($token);
				}
				else if($token->type == PSX_Html_Lexer_Token_Element::TYPE_END)
				{
					if($topToken->name == $token->name)
					{
						array_pop($this->stack);
					}
					else
					{
						// close the missing tag and hope that the next tokens
						// gets better
						$token = PSX_Html_Lexer_Token_Element::parse('/' . $topToken->name);

						$this->push($token);
					}
				}
			}
			else
			{
				$topToken->appendChild($token);
			}
		}
		else
		{
			// if the stack is empty add only an element token as root
			if($token instanceof PSX_Html_Lexer_Token_Element)
			{
				$this->stack[] = $this->root = $token;
			}
		}
	}

	public function getStack()
	{
		return $this->stack;
	}

	public function getTopToken()
	{
		return end($this->stack);
	}

	public function getRootElement()
	{
		return $this->root;
	}
}

