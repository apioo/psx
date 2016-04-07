<?php

namespace PSX\Project\Tests\Api\Population;

use PSX\Api\Parser\Raml;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Loader\Context;
use PSX\Record\RecordInterface;

class EntityRaml extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Project\Tests\Service\Population
     */
    protected $populationService;

    public function getDocumentation($version = null)
    {
        return Raml::fromFile(__DIR__ . '/../../Resource/population.raml', $this->context->get(Context::KEY_PATH));
    }

    protected function doGet()
    {
        return $this->populationService->get(
            $this->pathParameters['id']
        );
    }

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
