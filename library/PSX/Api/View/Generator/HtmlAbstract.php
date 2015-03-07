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

namespace PSX\Api\View\Generator;

use PSX\Api\View;
use PSX\Api\View\GeneratorInterface;
use PSX\Data\Schema\Generator\Html as HtmlGenerator;
use PSX\Data\SchemaInterface;

/**
 * HtmlAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
abstract class HtmlAbstract implements GeneratorInterface
{
	protected $modifier;

	public function __construct($modifier = null)
	{
		$this->modifier = $modifier;
	}

	public function setModifier($modifier)
	{
		$this->modifier = $modifier;
	}

	public function generate(View $view)
	{
		$class = strtolower(str_replace('\\', '-', get_class($this)));

		$html = '<div class="view ' . $class . '" data-status="' . $view->getStatus() . '" data-path="' . $view->getPath() . '">';
		$html.= '<h4>' . $this->getName() . '</h4>';

		foreach($view as $modifier => $schema)
		{
			if($this->modifier === null || $this->modifier & $modifier)
			{
				$result = $this->generateHtml($modifier, $schema, $view->getPath());

				if(!empty($result))
				{
					$html.= '<div class="view-schema" data-modifier="' . $modifier . '">';
					$html.= '<h5>' . View::getMethodName($modifier) . ' ' . View::getTypeName($modifier) . '</h5>';
					$html.= '<div class="view-schema-content">' . $result . '</div>';
					$html.= '</div>';
				}
			}
		}

		$html.= '</div>';

		return $html;
	}

	/**
	 * Returns the name of the html generator
	 *
	 * @return string
	 */
	abstract public function getName();

	/**
	 * Returns an html chunk for the specific schema
	 *
	 * @param integer $modifier
	 * @param PSX\Data\SchemaInterface $schema
	 * @param string $path
	 * @return string
	 */
	abstract public function generateHtml($modifier, SchemaInterface $schema, $path);
}
