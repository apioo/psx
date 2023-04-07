<?php

namespace App\Controller;

use App\Model;
use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\Tags;
use PSX\Framework\Base;
use PSX\Framework\Controller\ControllerAbstract;

class Welcome extends ControllerAbstract
{
    #[Get]
    #[Path('/')]
    #[Tags(['meta'])]
    public function show(): Model\Welcome
    {
        $welcome = new Model\Welcome();
        $welcome->setMessage('Welcome, your PSX installation is working!');
        $welcome->setUrl('https://phpsx.org');
        $welcome->setVersion(Base::getVersion());
        return $welcome;
    }
}
