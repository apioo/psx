<?php

namespace PSX\Project\Tests\Api\Population;

use PSX\Api\Parser\OpenAPI;
use PSX\Framework\Controller\SchemaApiAbstract;
use PSX\Framework\Loader\Context;

class CollectionOpenAPI extends SchemaApiAbstract
{
    /**
     * @Inject
     * @var \PSX\Project\Tests\Service\Population
     */
    protected $populationService;

    public function getDocumentation($version = null)
    {
        return OpenAPI::fromFile(__DIR__ . '/../../Resource/population.json', $this->context->get(Context::KEY_PATH));
    }

    protected function doGet()
    {
        return $this->populationService->getAll(
            $this->queryParameters->getProperty('startIndex'),
            $this->queryParameters->getProperty('count')
        );
    }

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
