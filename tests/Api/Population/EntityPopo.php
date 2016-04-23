<?php

namespace PSX\Project\Tests\Api\Population;

use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Record\RecordInterface;

/**
 * @Title("Population")
 * @Description("Entity endpoint")
 * @PathParam(name="id", type="integer", required=true)
 */
class EntityPopo extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Project\Tests\Service\Population
     */
    protected $populationService;

    /**
     * @Outgoing(code=200, schema="PSX\Project\Tests\Model\Entity")
     */
    protected function doGet()
    {
        return $this->populationService->get(
            $this->pathParameters['id']
        );
    }

    /**
     * @Incoming(schema="PSX\Project\Tests\Model\Entity")
     * @Outgoing(code=200, schema="PSX\Project\Tests\Model\Message")
     */
    protected function doPut($record)
    {
        $this->populationService->update(
            $this->pathParameters['id'],
            $record->getPlace(),
            $record->getRegion(),
            $record->getPopulation(),
            $record->getUsers(),
            $record->getWorldUsers()
        );

        return [
            'success' => true,
            'message' => 'Update successful',
        ];
    }

    /**
     * @Outgoing(code=200, schema="PSX\Project\Tests\Model\Message")
     */
    protected function doDelete($record)
    {
        $this->populationService->delete(
            $this->pathParameters['id']
        );

        return [
            'success' => true,
            'message' => 'Delete successful',
        ];
    }
}
