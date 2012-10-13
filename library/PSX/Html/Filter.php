<?php
/*
 *  $Id: Filter.php 611 2012-08-25 11:14:06Z k42b3.x@googlemail.com $
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
 * Filter html input based on an whitelist collection of allowed elements
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Html
 * @version    $Revision: 611 $
 */
class PSX_Html_Filter
{
	private $content;
	private $collection;

	private $elementListener = array();
	private $textListener    = array();

	public function __construct($content, PSX_Html_Filter_CollectionAbstract $collection = null)
	{
		if($collection === null)
		{
			$collection = new PSX_Html_Filter_Collection_Html5Basic();
		}

		$this->setContent($content);
		$this->setCollection($collection);
	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	public function setCollection(PSX_Html_Filter_CollectionAbstract $collection)
	{
		$this->collection = $collection;
	}

	public function addElementListener(PSX_Html_Filter_ElementListenerInterface $elementListener)
	{
		$this->elementListener[] = $elementListener;
	}

	public function addTextListener(PSX_Html_Filter_TextListenerInterface $textListener)
	{
		$this->textListener[] = $textListener;
	}

	/**
	 * Filters the given content based on the white-list of elements and returns
	 * the filtered result as string.
	 *
	 * @return string
	 */
	public function filter()
	{
		$str  = '';
		$root = PSX_Html_Lexer::parse('<html>' . $this->content . '</html>');

		if($root !== null)
		{
			// filter root element
			foreach($root->childNodes as $key => $el)
			{
				if($el instanceof PSX_Html_Lexer_Token_Element)
				{
					// filter value
					if($this->filterElement($el) === false)
					{
						unset($root->childNodes[$key]);

						continue;
					}

					// call element listener
					foreach($this->elementListener as $listener)
					{
						$result = $listener->onElement($el);

						if($result === false)
						{
							unset($root->childNodes[$key]);
						}
						else if($result instanceof PSX_Html_Lexer_Token_Element)
						{
							$root->childNodes[$key] = $result;
						}
					}
				}
				else if($el instanceof PSX_Html_Lexer_Token_Text)
				{
					// call text listener
					foreach($this->textListener as $listener)
					{
						$result = $listener->onText($el);

						if($result === false)
						{
							unset($root->childNodes[$key]);
						}
						else if($result instanceof PSX_Html_Lexer_Token_Text)
						{
							$root->childNodes[$key] = $result;
						}
					}
				}
			}

			// generate output
			foreach($root->childNodes as $el)
			{
				$str.= $el->__toString();
			}
		}

		return $str;
	}

	private function filterElement(PSX_Html_Lexer_Token_Element $element)
	{
		if(!isset($this->collection[$element->name]))
		{
			return false;
		}

		// check attributes
		if(!empty($element->attr))
		{
			$allowedAttr = $this->collection[$element->name]->getAttributes();

			foreach($element->attr as $key => $val)
			{
				if(isset($allowedAttr[$key]))
				{
					if($allowedAttr[$key] instanceof PSX_FilterAbstract)
					{
						$val = $allowedAttr[$key]->apply($val);

						if($val === false)
						{
							unset($element->attr[$key]);
						}
						else if($val === true)
						{
							// keep value as it is
						}
						else
						{
							$element->attr[$key] = $val;
						}
					}
					else if(is_string($allowedAttr[$key]))
					{
						$element->attr[$key] = $allowedAttr[$key];
					}
					else if(is_array($allowedAttr[$key]))
					{
						foreach($allowedAttr[$key] as $filter)
						{
							$val = $filter->apply($val);

							if($val === false)
							{
								unset($element->attr[$key]);

								break;
							}
						}

						if($val === true)
						{
							// keep value as it is
						}
						else if($val !== false)
						{
							$element->attr[$key] = $val;
						}
					}
					else
					{
						unset($element->attr[$key]);
					}
				}
				else
				{
					unset($element->attr[$key]);
				}
			}
		}

		// check childs
		foreach($element->childNodes as $key => $el)
		{
			// allow every whitespace to keep format
			if($el instanceof PSX_Html_Lexer_Token_Text && $el->isWhitespace())
			{
				continue;
			}

			// if element is not allowed as child
			if(!in_array($el->getName(), $this->collection[$element->name]->getValues()))
			{
				unset($element->childNodes[$key]);

				continue;
			}

			if($el instanceof PSX_Html_Lexer_Token_Element)
			{
				// filter value
				if($this->filterElement($el) === false)
				{
					unset($element->childNodes[$key]);

					continue;
				}

				// call element listener
				foreach($this->elementListener as $listener)
				{
					$result = $listener->onElement($el);

					if($result === false)
					{
						unset($element->childNodes[$key]);
					}
					else if($result instanceof PSX_Html_Lexer_Token_Element)
					{
						$element->childNodes[$key] = $result;
					}
				}
			}
			else if($el instanceof PSX_Html_Lexer_Token_Text)
			{
				// call text listener
				foreach($this->textListener as $listener)
				{
					$result = $listener->onText($el);

					if($result === false)
					{
						unset($element->childNodes[$key]);
					}
					else if($result instanceof PSX_Html_Lexer_Token_Text)
					{
						$element->childNodes[$key] = $result;
					}
				}
			}
		}

		return $element;
	}
}
