<?php
/*
 *  $Id: Sql.php 480 2012-05-01 18:13:54Z k42b3.x@googlemail.com $
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

namespace PSX\OpenId\Store;

use PSX\DateTime;
use PSX\OpenId\Provider\Association;
use PSX\OpenId\StoreInterface;
use PSX\Sql;
use PSX\Sql\Condition;

/**
 * PSX_OpenId_Store_Sql
 *
 * <code>
 * CREATE TABLE IF NOT EXISTS `amun_system_assoc` (
 *  `id` int(10) NOT NULL AUTO_INCREMENT,
 *  `opEndpoint` varchar(256) NOT NULL,
 *  `assocHandle` varchar(512) NOT NULL,
 *  `assocType` enum('HMAC-SHA1','HMAC-SHA256') NOT NULL,
 *  `sessionType` enum('DH-SHA1','DH-SHA256') NOT NULL,
 *  `secret` varchar(256) NOT NULL,
 *  `expires` int(10) NOT NULL,
 *  `date` datetime NOT NULL,
 *  PRIMARY KEY (`id`)
 * ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 * </code>
 *
 * @author     Christoph Kappestein <k42b3.x@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl.html GPLv3
 * @link       http://phpsx.org
 * @category   PSX
 * @package    PSX_OpenId
 * @version    $Revision: 480 $
 */
class Sql implements StoreInterface
{
	private $sql;
	private $table;

	public function __construct(Sql $sql, $table)
	{
		$this->sql   = $sql;
		$this->table = $table;
	}

	public function load($opEndpoint)
	{
		$sql = <<<SQL
SELECT
	`assocHandle`,
	`assocType`,
	`sessionType`,
	`secret`,
	`expires`
FROM
	{$this->table}
WHERE
	`opEndpoint` = ?
SQL;

		$row = $this->sql->getRow($sql, array($opEndpoint));

		if(!empty($row))
		{
			$assoc = new Association();
			$assoc->setAssocHandle($row['assocHandle']);
			$assoc->setAssocType($row['assocType']);
			$assoc->setSessionType($row['sessionType']);
			$assoc->setSecret($row['secret']);
			$assoc->setExpire($row['expires']);

			return $assoc;
		}

		return null;
	}

	public function loadByHandle($opEndpoint, $assocHandle)
	{
		$sql = <<<SQL
SELECT
	`assocHandle`,
	`assocType`,
	`sessionType`,
	`secret`,
	`expires`
FROM
	{$this->table}
WHERE
	`opEndpoint` = ?
AND
	`assocHandle` = ?
SQL;

		$row = $this->sql->getRow($sql, array($opEndpoint, $assocHandle));

		if(!empty($row))
		{
			$assoc = new Association();
			$assoc->setAssocHandle($row['assocHandle']);
			$assoc->setAssocType($row['assocType']);
			$assoc->setSessionType($row['sessionType']);
			$assoc->setSecret($row['secret']);
			$assoc->setExpire($row['expires']);

			return $assoc;
		}

		return null;
	}

	public function remove($opEndpoint, $assocHandle)
	{
		$con = new Condition();
		$con->add('opEndpoint', '=', $opEndpoint);
		$con->add('assocHandle', '=', $assocHandle);

		$this->sql->delete($con);
	}

	public function save($opEndpoint, Association $assoc)
	{
		$now = new DateTime();

		$this->sql->insert($this->table, array(

			'opEndpoint'  => $opEndpoint,
			'assocHandle' => $assoc->getAssocHandle(),
			'assocType'   => $assoc->getAssocType(),
			'sessionType' => $assoc->getSessionType(),
			'secret'      => $assoc->getSecret(),
			'expires'     => $assoc->getExpire(),
			'date'        => $now->format(DateTime::SQL),

		));
	}
}
