<?php
/*
 *  $Id: Lexer.php 609 2012-08-25 11:13:31Z k42b3.x@googlemail.com $
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
 * Robust html5 parser to parse non well-formed markup. It splits the html in to
 * an element / text token stream wich is pushed into an dom builder who builds
 * the tree structure depending on the incomming tokens. If a closing tag is
 * missing the dom build automatically closes the tag. Here an example howto get
 * all links from an html markup.
 * <code>
 * $root = PSX_Html_Lexer::parse($html);
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
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Html
 * @version    $Revision: 609 $
 */
class PSX_Html_Lexer
{
	const STATE_LT  = 0x1; // lower then
	const STATE_GT  = 0x2; // greater then

	const STATE_KEY = 0x3;
	const STATE_VAL = 0x4;

	const MODE_PARSE  = 0x1;
	const MODE_IGNORE = 0x2;

	/**
	 * Tags wich will be automatically closed if an new token is found
	 *
	 * @var array
	 */
	private static $shortTags = array('area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'menuitem', 'meta', 'param', 'source', 'track', 'wbr');

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
	 * @return PSX_Html_Lexer_Token_Element
	 */
	public static function parse($html)
	{
		$dom   = new PSX_Html_Lexer_Dom();
		$len   = strlen($html);
		$tag   = null;
		$text  = null;
		$state = null;
		$mode  = self::MODE_PARSE;

		$ignoreToken = null;
		$inQuote     = false;
		$quoteSign   = null;

		for($i = 0; $i < $len; $i++)
		{
			if($inQuote === true)
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
						$token = PSX_Html_Lexer_Token_Text::parse($text);

						if($token !== null)
						{
							$dom->push($token);
						}
					}

					$text  = null;
					$tag   = null;
					$state = self::STATE_LT;

					continue;
				}
			}
			else if($html[$i] == '>')
			{
				if($mode === self::MODE_PARSE)
				{
					if($tag !== null)
					{
						$token = PSX_Html_Lexer_Token_Element::parse($tag);

						if($token !== null)
						{
							// close tag automatically if top token is a short tag
							$topToken = $dom->getTopToken();

							if($topToken !== false)
							{
								if($token->type === PSX_Html_Lexer_Token_Element::TYPE_START)
								{
									if(in_array($topToken->name, self::$shortTags))
									{
										$dom->push(PSX_Html_Lexer_Token_Element::parse('/' . $topToken->name));
									}
								}
								else if($token->type === PSX_Html_Lexer_Token_Element::TYPE_END)
								{
									if(in_array($topToken->name, self::$shortTags) && $token->name != $topToken->name)
									{
										$dom->push(PSX_Html_Lexer_Token_Element::parse('/' . $topToken->name));
									}
								}
							}

							// push token to the dom
							$dom->push($token);

							// if we have an short token autmoatically close tag
							if($token->type === PSX_Html_Lexer_Token_Element::TYPE_START && $token->isShort())
							{
								$dom->push(PSX_Html_Lexer_Token_Element::parse('/' . $token->name));
							}
							// enter ignore mode if we have found an no parse
							// start tag
							else if($token->type === PSX_Html_Lexer_Token_Element::TYPE_START)
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
					$state = self::STATE_GT;

					continue;
				}
			}

			if($mode === self::MODE_PARSE)
			{
				if($state == self::STATE_LT)
				{
					$tag.= $html[$i];

					if($html[$i] == '"' || $html[$i] == '\'')
					{
						if($inQuote === false)
						{
							$inQuote   = true;
							$quoteSign = $html[$i];
						}
						else
						{
							$inQuote   = false;
							$quoteSign = null;
						}
					}
				}
				else if($state == self::STATE_GT)
				{
					$text.= $html[$i];
				}
			}
			else if($mode === self::MODE_IGNORE)
			{
				$text.= $html[$i];
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
