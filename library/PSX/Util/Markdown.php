<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Util;

/**
 * This parser implements a subset of the markdown syntax. It is optimized for
 * speed and uses almost no regular expressions for parsing.
 * <code>
 * $html = Markdown::decode($text);
 * </code>
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class Markdown
{
	const NONE           = 0x0;
	const PARAGRAPH      = 0x1;
	const UNORDERED_LIST = 0x3;
	const ORDERED_LIST   = 0x4;
	const CODE           = 0x5;

	public static $blockElements = array('br', 'hr', 'p', 'ol', 'ul', 'div', 'pre', 'blockquote', 'dl', 'table');

	protected $lines;
	protected $inQuote = false;
	protected $quote   = 0;
	protected $tag     = 0;

	public function __construct(array $lines)
	{
		$this->lines = $lines;
	}

	public function parse()
	{
		$html = '';

		foreach($this->lines as $k => $v)
		{
			$html.= $this->parseLine($v);
		}

		return $html;
	}

	protected function parseLine($v, $inQuote = false)
	{
		// blank line
		if(strlen(trim($v)) === 0 && ($this->tag != self::CODE || $v == ''))
		{
			return $this->endTag($inQuote);
		}

		switch(true)
		{
			case $v[0] == '>':

				$html = '';

				if($inQuote === false && $this->quote === 0)
				{
					$html.= $this->endTag($inQuote);
					$html.= '<blockquote>' . "\n";
				}

				$this->quote++;

				$v = $v[1] == ' ' ? substr($v, 2) : substr($v, 1);

				$html.= $this->parseLine($v, true);

				return $html;
				break;

			case $v[0] == '#':

				return $this->heading($v, $inQuote);
				break;

			case substr($v, 0, 4) == '    ':

				return $this->code(substr($v, 4), $inQuote);
				break;

			case substr($v, 0, 2) == '--':

				return $this->line(substr($v, 2), $inQuote);
				break;

			case $v[0] == '*':
			case $v[0] == '+':
			case $v[0] == '-':

				return $this->unorderedList(substr($v, 1), $inQuote);
				break;

			case (ctype_digit($v[0]) && $v[1] == '.') || (ctype_digit(substr($v, 2)) && $v[2] == '.'):

				// remove number and dot
				$p = strpos($v, '.');
				$v = $p !== false ? substr($v, $p + 1) : substr($v, 1);

				return $this->orderedList($v, $inQuote);
				break;

			case substr($v, 0, 2) == '  ':

				return $this->process($v, $inQuote);
				break;

			default:

				return $this->paragraph($v, $inQuote);
				break;
		}
	}

	protected function endTag($inQuote)
	{
		$html = '';

		switch($this->tag)
		{
			case self::PARAGRAPH:
				$html.= '</p>' . "\n";
				break;

			case self::UNORDERED_LIST:
				$html.= '</ul>' . "\n";
				break;

			case self::ORDERED_LIST:
				$html.= '</ol>' . "\n";
				break;

			case self::CODE:
				$html.= '</pre>' . "\n";
				break;
		}

		if($inQuote === false && $this->quote > 0)
		{
			$html.= '</blockquote>' . "\n";
			$this->quote = 0;
		}

		$this->tag = self::NONE;

		return $html;
	}

	protected function process($v, $inQuote)
	{
		switch($this->tag)
		{
			case self::PARAGRAPH:
				return $this->paragraph($v, $inQuote);
				break;

			case self::UNORDERED_LIST:
				return $this->unorderedList($v, $inQuote);
				break;

			case self::ORDERED_LIST:
				return $this->orderedList($v, $inQuote);
				break;

			case self::CODE:
				return $this->code($v, $inQuote);
				break;
		}
	}

	protected function heading($v, $inQuote = false)
	{
		$html = '';
		$html.= $this->endTag($inQuote);

		for($j = 6; $j > 0; $j--)
		{
			if(substr($v, 0, $j) == str_repeat('#', $j))
			{
				$html.= '<h' . $j . '>' . $this->text(substr($v, $j)) . '</h' . $j . '>' . "\n";
				break;
			}
		}

		return $html;
	}

	protected function paragraph($v, $inQuote = false)
	{
		$html = '';

		if($this->tag != self::PARAGRAPH || ($inQuote === false && $this->quote > 0))
		{
			$html.= $this->endTag($inQuote);
			$html.= '<p>';

			$this->tag = self::PARAGRAPH;
		}

		if(substr($v, -2) == '  ')
		{
			$v.= '<br />';
		}

		$html.= $this->text($v) . ' ';

		return $html;
	}

	protected function unorderedList($v, $inQuote = false)
	{
		$html = '';

		if($this->tag != self::UNORDERED_LIST || ($inQuote === false && $this->quote > 0))
		{
			$html.= $this->endTag($inQuote);
			$html.= '<ul>' . "\n";

			$this->tag = self::UNORDERED_LIST;
		}

		$html.= "\t" . '<li>' . $this->text($v) . '</li>' . "\n";

		return $html;
	}

	protected function orderedList($v, $inQuote = false)
	{
		$html = '';

		if($this->tag != self::ORDERED_LIST || ($inQuote === false && $this->quote > 0))
		{
			$html.= $this->endTag($inQuote);
			$html.= '<ol>' . "\n";

			$this->tag = self::ORDERED_LIST;
		}

		$html.= "\t" . '<li>' . $this->text($v) . '</li>' . "\n";

		return $html;
	}

	protected function code($v, $inQuote = false)
	{
		$html = '';

		if($this->tag != self::CODE || ($inQuote === false && $this->quote > 0))
		{
			$html.= $this->endTag($inQuote);
			$html.= '<pre class="prettyprint">';

			$this->tag = self::CODE;
		}

		$html.= htmlspecialchars($v, ENT_NOQUOTES) . "\n";

		return $html;
	}

	protected function line($v, $inQuote = false)
	{
		$html = '';
		$html.= $this->endTag($inQuote);
		$html.= '<hr />' . "\n";

		return $html;
	}

	protected function text($v)
	{
		return self::encodeEmphasis(trim($v));
	}

	public static function decode($content)
	{
		if(self::shouldDecoded($content))
		{
			$content  = self::normalize($content);
			$lines    = explode("\n", $content);

			$markdown = new self($lines);

			return $markdown->parse();
		}
		else
		{
			return $content;
		}
	}

	/**
	 * If the content contains any block level element we do not parse the text
	 * to avoid double encoding.
	 *
	 * @return boolean
	 */
	protected static function shouldDecoded($content)
	{
		foreach(self::$blockElements as $el)
		{
			if(strpos($content, '<' . $el) !== false)
			{
				return false;
			}
		}

		return true;
	}

	protected static function encodeEmphasis($v)
	{
		// encode emphasis only if we are not in an url
		$parts = preg_split('/(https?:\/\/\S*)/S', $v, -1, PREG_SPLIT_DELIM_CAPTURE);
		$html  = '';

		foreach($parts as $i => $part)
		{
			if($i % 2 == 0)
			{
				$part = preg_replace('/((\_\_|\*\*)(\w+)(\_\_|\*\*))/', '<b>$3</b>', $part);
				$part = preg_replace('/((\_|\*)(\w+)(\_|\*))/', '<i>$3</i>', $part);
			}

			$html.= $part;
		}

		return $html;
	}

	protected static function normalize($content)
	{
		$content = preg_replace('{^\xEF\xBB\xBF|\x1A}', '', $content);
		$content = str_replace(array("\r\n", "\n", "\r"), "\n", $content);
		$content = str_replace("\t", '    ', $content);
		$content = $content . "\n";

		return $content;
	}
}
