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

namespace PSX\Api\View\Generator\Html\Sample\Loader;

use DOMDocument;
use DOMXPath;
use PSX\Api\View;
use PSX\Api\View\Generator\Html\Sample\LoaderInterface;

/**
 * XmlFile
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
class XmlFile implements LoaderInterface
{
	protected $file;

	private $_doc;

	public function __construct($file)
	{
		$this->file = $file;
	}

	public function get($modifier, $path)
	{
		$samples = $this->getDocument()->getElementsByTagName('sample');

		foreach($samples as $sample)
		{
			$samplePath   = $sample->getAttribute('path');
			$sampleMethod = strtoupper($sample->getAttribute('method'));
			$sampleType   = strtolower($sample->getAttribute('type'));
			$sampleLang   = $sample->getAttribute('lang');

			if(!empty($samplePath) && !empty($sampleMethod) && !empty($sampleType) && !empty($sampleLang))
			{
				if($samplePath == $path && $sampleMethod == View::getMethodName($modifier) && $sampleType == strtolower(View::getTypeName($modifier)))
				{
					return '<pre><code class="' . $sampleLang . '">' . htmlspecialchars($sample->textContent) . '</code></pre>';
				}
			}
		}

		return null;
	}

	protected function getDocument()
	{
		if($this->_doc !== null)
		{
			return $this->_doc;
		}

		$this->_doc = new DOMDocument();
		$this->_doc->load($this->file);

		return $this->_doc;
	}
}
