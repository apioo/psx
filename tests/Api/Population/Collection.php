<?php

namespace PSX\Project\Tests\Api\Population;

use PSX\Framework\Controller\Proxy\VersionController;

class Collection extends VersionController
{
    protected function getVersions()
    {
        return [
            1 => CollectionRaml::class,
            2 => CollectionAnnotation::class,
        ];
    }
}
