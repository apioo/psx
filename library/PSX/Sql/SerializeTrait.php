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

namespace PSX\Sql;

use Doctrine\DBAL\Connection;

/**
 * SerializeTrait
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
trait SerializeTrait
{
	protected function unserializeType($value, $type)
	{
		$type = (($type >> 20) & 0xFF) << 20;

		switch($type)
		{
			case TableInterface::TYPE_TINYINT:
			case TableInterface::TYPE_SMALLINT:
			case TableInterface::TYPE_MEDIUMINT:
			case TableInterface::TYPE_INT:
			case TableInterface::TYPE_BIGINT:
			case TableInterface::TYPE_BIT:
			case TableInterface::TYPE_SERIAL:
				return (int) $value;
				break;

			case TableInterface::TYPE_DECIMAL:
			case TableInterface::TYPE_FLOAT:
			case TableInterface::TYPE_DOUBLE:
			case TableInterface::TYPE_REAL:
				return (float) $value;
				break;

			case TableInterface::TYPE_BOOLEAN:
				return (bool) $value;
				break;

			case TableInterface::TYPE_DATE:
			case TableInterface::TYPE_DATETIME:
				return new \DateTime($value);
				break;

			default:
				return $value;
				break;
		}
	}

	protected function serializeType($value, $type)
	{
		$type = (($type >> 20) & 0xFF) << 20;

		switch($type)
		{
			case TableInterface::TYPE_BOOLEAN:
				return $value ? '1' : '0';
				break;

			case TableInterface::TYPE_DATE:
			case TableInterface::TYPE_DATETIME:
				return $value instanceof \DateTime ? $value->format('Y-m-d H:i:s') : (string) $value;
				break;

			default:
				return (string) $value;
				break;
		}
	}
}
