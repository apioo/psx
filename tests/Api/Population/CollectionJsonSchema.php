<?php

namespace PSX\Project\Tests\Api\Population;

use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Record\RecordInterface;

/**
 * @Title("Population")
 * @Description("Collection endpoint")
 */
class CollectionJsonSchema extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Project\Tests\Service\Population
     */
    protected $populationService;

    /**
     * @QueryParam(name="startIndex", type="integer")
     * @QueryParam(name="count", type="integer")
     * @Outgoing(code=200, schema="../../Resource/schema/population/collection.json")
     */
    protected function doGet()
    {
        return $this->populationService->getAll(
            $this->queryParameters->getProperty('startIndex'),
            $this->queryParameters->getProperty('count')
        );
    }

    /**
     * @Incoming(schema="../../Resource/schema/population/entity.json")
     * @Outgoing(code=201, schema="../../Resource/schema/population/message.json")
     */
    protected function doPost($record)
    {
        $this->populationService->create(
            $record['place'],
            $record['region'],
            $record['population'],
            $record['users'],
            $record['worldUsers']
        );

        return [
            'success' => true,
            'message' => 'Create population successful',
        ];
    }
}
