<?php
/*
 *  $Id: Condition.php 582 2012-08-15 21:27:02Z k42b3.x@googlemail.com $
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

namespace PSX\Sql;

/**
 * PSX_Sql_Condition
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_Sql
 * @version    $Revision: 582 $
 */
interface Connection
{
	// Doctrine DBAL / PDO interface
    function prepare($prepareString);
    function query();
    function quote($input, $type = \PDO::PARAM_STR);
    function exec($statement);
    function lastInsertId($name = null);
    function beginTransaction();
    function commit();
    function rollBack();
    function errorCode();
    function errorInfo();

    // PSX methods
    public function assoc($sql, array $params = array(), $class = 'stdClass', array $args = array());

    public function getAll($sql, array $params = array(), $mode = 0, $class = 'stdClass', array $args = array());
    public function getRow($sql, array $params = array(), $mode = 0, $class = 'stdClass', array $args = array());
    public function getCol($sql, array $params = array());
    public function getField($sql, array $params = array());

    public function select($table, array $fields, Condition $condition = null, $select = 0, $sortBy = null, $sortOrder = 0, $startIndex = null, $count = 32);
    public function insert($table, array $params, $modifier = 0);
    public function update($table, array $params, Condition $condition = null, $modifier = 0);
    public function replace($table, array $params, $modifier = 0);
    public function delete($table, Condition $condition = null, $modifier = 0);
    public function count($table, Condition $condition = null);
}
