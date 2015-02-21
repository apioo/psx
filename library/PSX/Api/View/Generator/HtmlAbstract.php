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

namespace PSX\Api\View\Generator;

use PSX\Api\View;
use PSX\Api\View\GeneratorInterface;
use PSX\Data\Schema\Generator\Html as HtmlGenerator;
use PSX\Data\SchemaInterface;

/**
 * HtmlAbstract
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
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
