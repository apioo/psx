<?php

namespace App\Controller;

use App\Model;
use App\Service;
use App\Table;
use PSX\Api\Attribute\Delete;
use PSX\Api\Attribute\Get;
use PSX\Api\Attribute\Path;
use PSX\Api\Attribute\Post;
use PSX\Api\Attribute\Put;
use PSX\Framework\Controller\ControllerAbstract;
use PSX\Sql\TableManagerInterface;

class Population extends ControllerAbstract
{
    private Service\Population $populationService;
    private Table\Population $populationTable;

    public function __construct(Service\Population $populationService, TableManagerInterface $tableManager)
    {
        $this->populationService = $populationService;
        $this->populationTable = $tableManager->getTable(Table\Population::class);
    }

    #[Get]
    #[Path('/population')]
    public function getAll(): Model\PopulationCollection
    {
        return $this->populationTable->getCollection();
    }

    #[Get]
    #[Path('/population/:id')]
    public function get(int $id): Model\Population
    {
        return $this->populationTable->getEntity($id);
    }

    #[Post]
    #[Path('/population')]
    public function create(Model\Population $payload): Model\Message
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
    public function update(int $id, Model\Population $payload): Model\Message
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
    public function delete(int $id): Model\Message
    {
        $id = $this->populationService->delete($id);

        $message = new Model\Message();
        $message->setSuccess(true);
        $message->setMessage('Population record successfully deleted');
        $message->setId($id);
        return $message;
    }
}
