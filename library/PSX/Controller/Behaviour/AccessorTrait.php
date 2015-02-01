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

namespace PSX\Controller\Behaviour;

use PSX\Data\Accessor;
use PSX\Data\TransformerInterface;

/**
 * Provides an unify way to access values from the request body
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link	http://phpsx.org
 */
trait AccessorTrait
{
	/**
	 * @Inject
	 * @var PSX\Data\Extractor
	 */
	protected $extractor;

	private $_accessor;

	/**
	 * Returns an accessor object with that you can easily access values from
	 * the request body
	 *
	 * @param PSX\Data\TransformerInterface $transformer
	 * @param string $readerType
	 * @return PSX\Data\Accessor
	 */
	protected function getAccessor(TransformerInterface $transformer = null, $readerType = null)
	{
		if($this->_accessor === null)
		{
			$data     = $this->extractor->extract($this->request, $transformer, $readerType);
			$accessor = new Accessor($this->validate, $data);

			$this->_accessor = $accessor;
		}

		return $this->_accessor;
	}
}
