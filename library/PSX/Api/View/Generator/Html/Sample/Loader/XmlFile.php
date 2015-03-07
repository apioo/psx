<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2015 Christoph Kappestein <k42b3.x@gmail.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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
 * @license http://www.apache.org/licenses/LICENSE-2.0
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
