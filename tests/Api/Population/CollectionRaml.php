<?php

namespace PSX\Project\Tests\Api\Population;

use PSX\Api\Parser\Raml;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Loader\Context;
use PSX\Record\RecordInterface;

class CollectionRaml extends SchemaApiAbstract
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
        return $this->populationService->getAll(
            $this->queryParameters->getProperty('startIndex'),
            $this->queryParameters->getProperty('count')
        );
    }

    protected function doPost(RecordInterface $record)
    {
        $this->populationService->create(
            $record['place'],
            $record['region'],
            $record['population'],
            $record['users'],
            $record['world_users']
        );

        return [
            'success' => true,
            'message' => 'Create population successful',
        ];
    }
}
