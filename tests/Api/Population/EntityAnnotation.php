<?php

namespace PSX\Project\Tests\Api\Population;

use PSX\Framework\Controller\AnnotationApiAbstract;
use PSX\Record\RecordInterface;

/**
 * @Title("Population")
 * @Description("Entity endpoint")
 * @PathParam(name="id", type="integer", required=true)
 */
class EntityAnnotation extends AnnotationApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Project\Tests\Service\Population
     */
    protected $populationService;

    /**
     * @Outgoing(code=200, schema="../../Resource/schema/population/entity.json")
     */
    protected function doGet()
    {
        return $this->populationService->get(
            $this->pathParameters['id']
        );
    }

    /**
     * @Incoming(schema="../../Resource/schema/population/entity.json")
     * @Outgoing(code=200, schema="../../Resource/schema/population/message.json")
     */
    protected function doPut(RecordInterface $record)
    {
        $this->populationService->update(
            $this->pathParameters['id'],
            $record['place'],
            $record['region'],
            $record['population'],
            $record['users'],
            $record['world_users']
        );

        return [
            'success' => true,
            'message' => 'Update successful',
        ];
    }

    /**
     * @Outgoing(code=200, schema="../../Resource/schema/population/message.json")
     */
    protected function doDelete(RecordInterface $record)
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
