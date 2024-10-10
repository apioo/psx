<?php

declare(strict_types = 1);

namespace App\Model;

/**
 * @extends Collection<Population>
 */
class PopulationCollection extends Collection implements \JsonSerializable, \PSX\Record\RecordableInterface
{
}

