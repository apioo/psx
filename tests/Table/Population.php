<?php

namespace PSX\Project\Tests\Table;

use PSX\Sql\TableAbstract;

class Population extends TableAbstract
{
    public function getName()
    {
        return 'population';
    }

    public function getColumns()
    {
        return array(
            'id'          => self::TYPE_INT | 10 | self::AUTO_INCREMENT | self ::PRIMARY_KEY,
            'place'       => self::TYPE_INT | 10,
            'region'      => self::TYPE_VARCHAR | 64,
            'population'  => self::TYPE_INT | 10,
            'users'       => self::TYPE_INT | 10,
            'world_users' => self::TYPE_FLOAT,
            'datetime'    => self::TYPE_DATETIME,
        );
    }
}
