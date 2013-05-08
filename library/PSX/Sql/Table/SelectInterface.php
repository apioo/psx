<?php
/*
 * psx
 * A object oriented and modular based PHP framework for developing
 * dynamic web applications. For the current version and informations
 * visit <http://phpsx.org>
 *
 * Copyright (c) 2010-2013 Christoph Kappestein <k42b3.x@gmail.com>
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

namespace PSX\Sql\Table;

/**
 * SelectInterface
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */
interface SelectInterface
{
	public function join($type, $table, $cardinality = 'n:1', $foreignKey = null);
	public function where($column, $operator, $value, $conjunction = 'AND');
	public function groupBy($column);
	public function orderBy($column, $sort = 0x1);
	public function limit($start, $count = null);

	public function getAll($mode = 0, $class = null, array $args = array());
	public function getRow($mode = 0, $class = null, array $args = array());
	public function getCol();
	public function getField();
	public function getTotalResults();

	public function getTable();
	public function getSql();
	public function getCondition();
	public function setPrefix($prefix);
	public function setColumns(array $columns);
	public function getSupportedFields();
	public function getSelfColumns();
	public function getAllColumns();
	public function getSelectedColumns();
	public function getAllSelectedColumns();
	public function buildJoins();
}

