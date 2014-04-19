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

use PSX\Exception;
use PSX\FilterAbstract;
use PSX\Html\Filter\CollectionAbstract;
use PSX\Html\Filter\Collection\Html5Text;
use PSX\Html\Filter\ElementListenerInterface;
use PSX\Html\Filter\TextListenerInterface;
use PSX\Html\Filter\CommentListenerInterface;
use PSX\Html\Lexer\Token\Comment;
use PSX\Html\Lexer\Token\Element;
use PSX\Html\Lexer\Token\Text;

/**
 * Filter html input based on an whitelist collection of allowed elements
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Filter
{
	const ANY_VALUE = 0x1;
	const CONTENT_TRANSPARENT = 0x1;

	private $content;
	private $collection;
	private $allowComments = false;

	private $elementListener = array();
	private $textListener    = array();
	private $commentListener = array();

	public function __construct($content, CollectionAbstract $collection = null)
	{
		if($collection === null)
		{
			$collection = new Html5Text();
		}

		$this->setContent($content);
		$this->setCollection($collection);
	}

	public function setContent($content)
	{
		$this->content = $content;
	}

	public function setCollection(CollectionAbstract $collection)
	{
		$this->collection = $collection;
	}

	public function setAllowComments($allowComments)
	{
		$this->allowComments = $allowComments;
	}

	public function addElementListener(ElementListenerInterface $elementListener)
	{
		$this->elementListener[] = $elementListener;
	}

	public function addTextListener(TextListenerInterface $textListener)
	{
		$this->textListener[] = $textListener;
	}

	public function addCommentListener(CommentListenerInterface $commentListener)
	{
		$this->commentListener[] = $commentListener;
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
		$root = Lexer::parse('<body>' . $this->content . '</body>');

		if($root !== null)
		{
			// filter root element
			$this->filterElementChilds($root, false);

			// generate output
			foreach($root->getChildNodes() as $el)
			{
				$str.= $el->__toString();
			}
		}

		return $str;
	}

	protected function filterElement(Element $element)
	{
		if(!isset($this->collection[$element->getName()]))
		{
			return false;
		}

		// filter attributes
		if($element->hasAttributes())
		{
			$this->filterElementAttributes($element);
		}

		// filter childs
		$this->filterElementChilds($element);

		return $element;
	}

	protected function filterElementAttributes(Element $element)
	{
		$allowedAttr = $this->collection[$element->getName()]->getAttributes();
		$allowedData = isset($allowedAttr['data-*']);

		foreach($element->getAttributes() as $key => $val)
		{
			if($allowedData && substr($key, 0, 5) == 'data-')
			{
				$dataKey = substr($key, 5);

				if(preg_match('/^[A-Za-z0-9-_.]{1,64}$/', $dataKey))
				{
					$key = 'data-*';
				}
			}

			if(isset($allowedAttr[$key]))
			{
				if($allowedAttr[$key] instanceof FilterAbstract)
				{
					$val = $allowedAttr[$key]->apply($val);

					if($val === false)
					{
						$element->removeAttribute($key);
					}
					else if($val === true)
					{
						// keep value as it is
					}
					else
					{
						$element->setAttribute($key, $val);
					}
				}
				else if(is_string($allowedAttr[$key]))
				{
					$element->setAttribute($key, $allowedAttr[$key]);
				}
				else if(is_array($allowedAttr[$key]))
				{
					foreach($allowedAttr[$key] as $filter)
					{
						if($filter instanceof FilterAbstract)
						{
							$val = $filter->apply($val);

							if($val === false)
							{
								$element->removeAttribute($key);
								break;
							}
						}
						else if((string) $filter === $element->getAttribute($key))
						{
							$val = true;
						}
					}

					if($val === true)
					{
						// keep value as it is
					}
					else if($val !== false)
					{
						$element->setAttribute($key, $val);
					}
				}
				else if($allowedAttr[$key] === self::ANY_VALUE)
				{
					// the attribute can contain any content
				}
				else
				{
					$element->removeAttribute($key);
				}
			}
			else
			{
				$element->removeAttribute($key);
			}
		}
	}

	protected function filterElementChilds(Element $element, $lookupChilds = true)
	{
		// get allowed childs. If the value is CONTENT_TRANSPARENT we look up 
		// the parent elements and get the allowed childrens
		if($lookupChilds)
		{
			$childs = $this->collection[$element->getName()]->getValues();

			if(!is_array($childs))
			{
				if($childs === self::CONTENT_TRANSPARENT)
				{
					$parentNode = $element;

					while($this->collection[$parentNode->getName()]->getValues() === self::CONTENT_TRANSPARENT)
					{
						$parentNode = $parentNode->getParentNode();
					}

					if($parentNode instanceof Element)
					{
						$childs = $this->collection[$parentNode->getName()]->getValues();
					}
					else
					{
						$childs = array();
					}
				}
				else
				{
					throw new Exception('Child must be either an array or PSX\Html\Filter constant');
				}
			}
		}

		// check childs
		foreach($element->getChildNodes() as $key => $el)
		{
			// allow every whitespace to keep format
			if($el instanceof Text && $el->isWhitespace())
			{
				continue;
			}

			if($lookupChilds === true && !in_array($el->getName(), $childs))
			{
				$element->removeChild($key);

				continue;
			}

			if($el instanceof Element)
			{
				// filter value
				if($this->filterElement($el) === false)
				{
					$element->removeChild($key);
					continue;
				}

				// call element listener
				foreach($this->elementListener as $listener)
				{
					$result = $listener->onElement($el);

					if($result === false)
					{
						$element->removeChild($key);
					}
					else if($result instanceof Element)
					{
						$element->setChild($key, $result);
					}
				}
			}
			else if($el instanceof Text)
			{
				// call text listener
				foreach($this->textListener as $listener)
				{
					$result = $listener->onText($el);

					if($result === false)
					{
						$element->removeChild($key);
					}
					else if($result instanceof Text)
					{
						$element->setChild($key, $result);
					}
				}
			}
			else if($el instanceof Comment)
			{
				if($this->allowComments)
				{
					// call comment listener
					foreach($this->commentListener as $listener)
					{
						$result = $listener->onComment($el);

						if($result === false)
						{
							$element->removeChild($key);
						}
						else if($result instanceof Comment)
						{
							$element->setChild($key, $result);
						}
					}
				}
				else
				{
					$element->removeChild($key);
				}
			}
		}
	}
}

