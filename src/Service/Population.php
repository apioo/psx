<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Service;

use App\Model;
use App\Table;
use PSX\Http\Exception as StatusCode;
use PSX\Sql\TableManagerInterface;

/**
 * Population
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Population
{
    private Table\Population $populationTable;

    public function __construct(TableManagerInterface $tableManager)
    {
        $this->populationTable = $tableManager->getTable(Table\Population::class);
    }

    public function create(Model\Population $payload): int
    {
        $row = new Table\Generated\PopulationRow();
        $row->setPlace($payload->getPlace() ?? throw new StatusCode\BadRequestException('No place provided'));
        $row->setRegion($payload->getRegion() ?? throw new StatusCode\BadRequestException('No region provided'));
        $row->setPopulation($payload->getPopulation() ?? throw new StatusCode\BadRequestException('No population provided'));
        $row->setUsers($payload->getUsers() ?? throw new StatusCode\BadRequestException('No users provided'));
        $row->setWorldUsers($payload->getWorldUsers() ?? throw new StatusCode\BadRequestException('No world users provided'));
        $row->setInsertDate(new \DateTime());
        $this->populationTable->create($row);

        return $this->populationTable->getLastInsertId();
    }

    public function update(int $id, Model\Population $payload): int
    {
        $row = $this->populationTable->find($id);
        if (!$row instanceof Table\Generated\PopulationRow) {
            throw new StatusCode\NotFoundException('Provided id does not exist');
        }

        $row->setPlace($payload->getPlace() ?? $row->getPlace());
        $row->setRegion($payload->getRegion() ?? $row->getRegion());
        $row->setPopulation($payload->getPopulation() ?? $row->getPopulation());
        $row->setUsers($payload->getUsers() ?? $row->getUsers());
        $row->setWorldUsers($payload->getWorldUsers() ?? $row->getWorldUsers());
        $this->populationTable->update($row);

        return $row->getId();
    }

    public function delete(int $id): int
    {
        $row = $this->populationTable->find($id);
        if (!$row instanceof Table\Generated\PopulationRow) {
            throw new StatusCode\NotFoundException('Provided id does not exist');
        }

        $this->populationTable->delete($row);

        return $row->getId();
    }
}
