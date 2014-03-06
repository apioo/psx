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

namespace PSX\Html;

use PSX\Html\Lexer\Dom;
use PSX\Html\Lexer\Token\Comment;
use PSX\Html\Lexer\Token\Text;
use PSX\Html\Lexer\Token\Element;

/**
 * Robust html5 parser to parse non well-formed markup. It splits the html in to
 * an element / text token stream wich is pushed into an dom builder who builds
 * the tree structure depending on the incomming tokens. If a closing tag is
 * missing the dom build automatically closes the tag. Here an example howto get
 * all links from an html markup.
 * <code>
 * $root = Lexer::parse($html);
 * $elements = $root->getElementsByTagName('a');
 *
 * foreach($elements as $el)
 * {
 *     $href = $el->getAttribute('href');
 *
 *     if($href !== false)
 *     {
 *         echo $href . "\n";
 *     }
 * }
 * </code>
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Lexer
{
	const INSIDE_TAG  = 0x1; // lower then
	const OUTSIDE_TAG = 0x2; // greater then

	const STATE_KEY = 0x3;
	const STATE_VAL = 0x4;

	const MODE_PARSE  = 0x1;
	const MODE_IGNORE = 0x2;

	/**
	 * @var array
	 */
	public static $spaceCharacters = array(0x20, 0x9, 0xA, 0xC, 0xD);

	/**
	 * Tags wich will be automatically closed if an new token is found
	 *
	 * @var array
	 */
	public static $voidTags = array('area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'menuitem', 'meta', 'param', 'source', 'track', 'wbr');

	/**
	 * Tags where the content will not be parsed until an explicit ending token
	 * was found. In the hmtl spec these are Raw text and RCDATA elements
	 *
	 * @var array
	 */
	private static $npTags = array('script', 'style', 'textarea', 'title');

	/**
	 * Main method wich will try to parse the given html into an tree structure.
	 * We split the content into either an element or text token. If we find an
	 * < sign we capture all content to the next > sign. The content will be
	 * then parsed as element token all other content will be treated as text
	 * token. All tokens are pushed to the dom wich builds the tree structure
	 * based on the incomming tokens.
	 *
	 * @return PSX\Html\Lexer\Token\Element
	 */
	public static function parse($html)
	{
		$dom   = new Dom();
		$len   = strlen($html);
		$tag   = null;
		$text  = null;
		$state = null;
		$mode  = self::MODE_PARSE;

		$ignoreToken = null;
		$inQuote     = false;
		$inComment   = false;
		$quoteSign   = null;

		for($i = 0; $i < $len; $i++)
		{
			// set quote switches if we are inside an element
			if($mode === self::MODE_PARSE && $state == self::INSIDE_TAG)
			{
				if($html[$i] == '"' || $html[$i] == '\'')
				{
					if($inQuote === false)
					{
						$inQuote   = true;
						$quoteSign = $html[$i];
					}
					else if($quoteSign == $html[$i])
					{
						$inQuote   = false;
						$quoteSign = null;
					}
				}
			}

			if($inQuote === true || $inComment === true)
			{
				// we capture all data in an attribute value i.e. title="foo > bar"
				// so that the > is ignored on parsing
			}
			else if($html[$i] == '<')
			{
				if($mode === self::MODE_IGNORE)
				{
					if($ignoreToken !== null && strtolower(substr($html, $i + 1, strlen($ignoreToken->name) + 1)) == '/' . $ignoreToken->name)
					{
						$ignoreToken = null;

						$mode = self::MODE_PARSE;
					}
				}

				if($mode === self::MODE_PARSE)
				{
					if($text !== null)
					{
						$token = Text::parse($text);

						if($token !== null)
						{
							$dom->push($token);
						}
					}

					$text = null;
					$tag  = null;

					if(substr($html, $i, 4) == '<!--')
					{
						$mode      = self::MODE_IGNORE;
						$inComment = true;
					}
					else
					{
						$state     = self::INSIDE_TAG;
						continue;
					}
				}
			}
			else if($html[$i] == '>')
			{
				if($mode === self::MODE_PARSE)
				{
					if($tag !== null)
					{
						$token = Element::parse($tag);

						if($token !== null)
						{
							// push token to the dom
							$dom->push($token);

							// if we have an no parse element enter ignore mode
							if($token->type === Element::TYPE_START)
							{
								if(in_array($token->name, self::$npTags))
								{
									$ignoreToken = $token;

									$mode = self::MODE_IGNORE;
								}
							}
						}
					}

					$tag   = null;
					$text  = null;
					$state = self::OUTSIDE_TAG;
					continue;
				}
			}

			if($mode === self::MODE_PARSE)
			{
				if($state == self::INSIDE_TAG)
				{
					$tag.= $html[$i];
				}
				else if($state == self::OUTSIDE_TAG)
				{
					$text.= $html[$i];
				}
			}
			else if($mode === self::MODE_IGNORE)
			{
				$text.= $html[$i];

				if($inComment && $i > 2 && ($html[$i - 2] == '-' && $html[$i - 1] == '-' && $html[$i] == '>'))
				{
					$inComment = false;
					$token     = Comment::parse($text);

					if($token !== null)
					{
						$dom->push($token);
					}

					$tag  = null;
					$text = null;
					$mode = self::MODE_PARSE;
				}
			}
		}

		return $dom->getRootElement();
	}

	/**
	 * Parses an attribute string and tries to extract the key value pairs into
	 * an array. Is used by the element token to extract the attribute values
	 *
	 * @param string $html
	 * @return array
	 */
	public static function parseAttributes($html)
	{
		$attr = array();

		if(!empty($html))
		{
			$len   = strlen($html);
			$key   = null;
			$val   = null;
			$state = self::STATE_KEY;
			$mode  = self::MODE_PARSE;

			$ignoreSign = null;

			for($i = 0; $i < $len; $i++)
			{
				if($mode == self::MODE_PARSE)
				{
					if($state == self::STATE_KEY && $html[$i] == '=')
					{
						$key = strtolower(trim($key));

						if(!empty($key))
						{
							$attr[$key] = null;

							$key = null;
						}

						$state = self::STATE_VAL;

						continue;
					}
					else if($state == self::STATE_KEY && trim($html[$i]) == '')
					{
						$key = strtolower(trim($key));

						if(!empty($key))
						{
							$attr[$key] = null;

							$key = null;
						}

						continue;
					}
					else if($state == self::STATE_VAL && ($html[$i] == '"' || $html[$i] == '\''))
					{
						$ignoreSign = $html[$i];

						$val  = null;
						$mode = self::MODE_IGNORE;

						continue;
					}
					else if($state == self::STATE_VAL && trim($html[$i]) == '')
					{
						if(trim($val) != '')
						{
							$state = self::STATE_KEY;

							end($attr);

							$attr[key($attr)] = $val;

							$val = null;
						}

						continue;
					}
				}
				else if($mode == self::MODE_IGNORE)
				{
					if($ignoreSign !== null && $html[$i] == $ignoreSign)
					{
						$ignoreSign = null;

						$mode  = self::MODE_PARSE;
						$state = self::STATE_KEY;

						if(trim($val) != '')
						{
							end($attr);

							$attr[key($attr)] = $val;

							$val = null;
						}

						continue;
					}
				}

				if($mode == self::MODE_PARSE)
				{
					if($state == self::STATE_KEY)
					{
						$key.= $html[$i];
					}
					else if($state == self::STATE_VAL)
					{
						$val.= $html[$i];
					}
				}
				else if($mode == self::MODE_IGNORE)
				{
					$val.= $html[$i];
				}
			}

			// if something is in key add it as empty attribute
			if($state == self::STATE_KEY)
			{
				$key = strtolower(trim($key));

				if(!empty($key))
				{
					$attr[$key] = null;
				}
			}
			else if($state == self::STATE_VAL)
			{
				end($attr);

				$attr[key($attr)] = $val;
			}
		}

		return $attr;
	}
}
