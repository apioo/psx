<?php

namespace PSX\Project\Tests\Api\Population;

use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Annotation\Inject;

/**
 * @Title("Population")
 * @Description("Collection endpoint")
 */
class CollectionPopo extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Project\Tests\Service\Population
     */
    protected $populationService;

    /**
     * @QueryParam(name="startIndex", type="integer")
     * @QueryParam(name="count", type="integer")
     * @Outgoing(code=200, schema="PSX\Project\Tests\Model\Collection")
     */
    protected function doGet()
    {
        return $this->populationService->getAll(
            $this->queryParameters->getProperty('startIndex'),
            $this->queryParameters->getProperty('count')
        );
    }

    /**
     * @Incoming(schema="PSX\Project\Tests\Model\Entity")
     * @Outgoing(code=201, schema="PSX\Project\Tests\Model\Message")
     */
    protected function doPost($record)
    {
        $this->populationService->create(
            $record->getPlace(),
            $record->getRegion(),
            $record->getPopulation(),
            $record->getUsers(),
            $record->getWorldUsers()
        );

        return [
            'success' => true,
            'message' => 'Create population successful',
        ];
    }
}
