<?php
/*
 *  $Id: Parse.php 608 2012-08-25 11:13:12Z k42b3.x@googlemail.com $
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
 * This class is for parsing html. It is especially designed for parsing invalid
 * html. It uses the html lexer and parses only the header or body part
 * dependening on the value wich you want fetch
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Html
 * @version    $Revision: 608 $
 */
class PSX_Html_Parse
{
	private $content;

	public function __construct($content)
	{
		$this->content = $content;
	}

	public function getHead()
	{
		return self::getContent($this->content, 'head');
	}

	public function getBody()
	{
		return self::getContent($this->content, 'body');
	}

	/**
	 * This method search for the PSX_Html_Parse_Element in the head section
	 * of an html document. If the element is found it returns the attribute
	 * $return or false if its not available.
	 *
	 * @param PSX_Html_Parse_Element $element
	 * @param string $return
	 * @return false|string
	 */
	public function fetchAttrFromHead(PSX_Html_Parse_Element $element, $return = false)
	{
		$root = PSX_Html_Lexer::parse($this->getHead());

		if($root instanceof PSX_Html_Lexer_Token_Element)
		{
			$elements = $root->getElementsByTagName($element->getName());

			foreach($elements as $el)
			{
				$found = true;
				$attr  = $element->getAttributes();

				foreach($attr as $key => $value)
				{
					$val = $el->getAttribute($key);

					if($val === false || strcasecmp($val, $value) != 0)
					{
						$found = false;

						break;
					}
				}

				if($found === true)
				{
					return $el->getAttribute($return);
				}
			}
		}

		return false;
	}

	/**
	 * Extracts the content part of an given $tag. I.e. if you want only parse
	 * the head section
	 *
	 * @param string $content
	 * @param string $tag
	 * @return string
	 */
	public static function getContent($content, $tag)
	{
		$posStart = stripos($content, '<' . $tag);

		if($posStart !== false)
		{
			$content = substr($content, $posStart);
			$posEnd  = stripos($content, '</' . $tag . '>');

			if($posEnd !== false)
			{
				$data = substr($content, 0, $posEnd + strlen($tag) + 3);

				return $data;
			}
		}

		return null;
	}
}

