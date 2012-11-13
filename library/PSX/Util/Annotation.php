<?php
/*
 *  $Id: Uuid.php 602 2012-08-25 11:10:34Z k42b3.x@googlemail.com $
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
 * Util class to parse docblooks annotations
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @see        http://www.ietf.org/rfc/rfc4122.txt
 * @category   PSX
 * @package    PSX_Util
 * @version    $Revision: 602 $
 */
class PSX_Util_Annotation
{
	public static function parse($doc)
	{
		$block = new PSX_Util_Annotation_DocBlock();
		$lines = explode("\n", $doc);
		$text  = '';

		// remove first line
		unset($lines[0]);

		foreach($lines as $line)
		{
			$line = trim($line);
			$line = substr($line, 2);

			if($line[0] == '@')
			{
				$line  = substr($line, 1);
				$pos   = strpos($line, ' ');

				if($pos !== false)
				{
					$key   = substr($line, 0, $pos);
					$value = substr($line, $pos);
				}
				else
				{
					$key   = $line;
					$value = null;
				}

				$key   = trim($key);
				$value = trim($value);

				if(!empty($key))
				{
					$block->addAnnotation($key, $value);
				}
			}
			else
			{
				$text.= trim($line) . "\n";
			}
		}

		$block->setText($text);

		return $block;
	}
}

