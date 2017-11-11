<?php

namespace PSX\Project\Tests\Api\Population;

use PSX\Framework\Controller\Proxy\VersionController;

class Entity extends VersionController
{
    protected function getVersions()
    {
        return [
            1 => EntityPopo::class,
        ];
    }
}
