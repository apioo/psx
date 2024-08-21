<?php

namespace App\Controller;

use App\Model;
use App\Service;
use App\Table;
use PSX\Api\Attribute\Body;
use PSX\Api\Attribute\Delete;
use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Param;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\Post;
use PSX\Api\Attribute\Put;
use PSX\Api\Attribute\Query;
use PSX\Framework\Controller\ControllerAbstract;

class Population extends ControllerAbstract
{
    private Service\Population $populationService;
    private Table\Population $populationTable;

    public function __construct(Service\Population $populationService, Table\Population $populationTable)
    {
        $this->populationService = $populationService;
        $this->populationTable = $populationTable;
    }

    #[Get]
    #[Path('/population')]
    public function getAll(#[Query] ?int $startIndex = null, #[Query] ?int $count = null): Model\PopulationCollection
    {
        return $this->populationTable->getCollection($startIndex, $count);
    }

    #[Get]
    #[Path('/population/:id')]
    public function get(#[Param] int $id): Model\Population
    {
        return $this->populationTable->getEntity($id);
    }

    #[Post]
    #[Path('/population')]
    public function create(#[Body] Model\Population $payload): Model\Message
    {
        $id = $this->populationService->create($payload);

        $message = new Model\Message();
        $message->setSuccess(true);
        $message->setMessage('Population record successfully created');
        $message->setId($id);
        return $message;
    }

    #[Put]
    #[Path('/population/:id')]
    public function update(#[Param] int $id, #[Body] Model\Population $payload): Model\Message
    {
        $id = $this->populationService->update($id, $payload);

        $message = new Model\Message();
        $message->setSuccess(true);
        $message->setMessage('Population record successfully updated');
        $message->setId($id);
        return $message;
    }

    #[Delete]
    #[Path('/population/:id')]
    public function delete(#[Param] int $id): Model\Message
    {
        $id = $this->populationService->delete($id);

        $message = new Model\Message();
        $message->setSuccess(true);
        $message->setMessage('Population record successfully deleted');
        $message->setId($id);
        return $message;
    }
}
