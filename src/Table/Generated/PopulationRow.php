<?php

namespace App\Table\Generated;

class PopulationRow implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    private ?int $id = null;
    private ?int $place = null;
    private ?string $region = null;
    private ?int $population = null;
    private ?int $users = null;
    private ?float $worldUsers = null;
    private ?\PSX\DateTime\LocalDateTime $insertDate = null;
    public function setId(int $id) : void
    {
        $this->id = $id;
    }
    public function getId() : int
    {
        return $this->id ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "id" was provided');
    }
    public function setPlace(int $place) : void
    {
        $this->place = $place;
    }
    public function getPlace() : int
    {
        return $this->place ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "place" was provided');
    }
    public function setRegion(string $region) : void
    {
        $this->region = $region;
    }
    public function getRegion() : string
    {
        return $this->region ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "region" was provided');
    }
    public function setPopulation(int $population) : void
    {
        $this->population = $population;
    }
    public function getPopulation() : int
    {
        return $this->population ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "population" was provided');
    }
    public function setUsers(int $users) : void
    {
        $this->users = $users;
    }
    public function getUsers() : int
    {
        return $this->users ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "users" was provided');
    }
    public function setWorldUsers(float $worldUsers) : void
    {
        $this->worldUsers = $worldUsers;
    }
    public function getWorldUsers() : float
    {
        return $this->worldUsers ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "world_users" was provided');
    }
    public function setInsertDate(\PSX\DateTime\LocalDateTime $insertDate) : void
    {
        $this->insertDate = $insertDate;
    }
    public function getInsertDate() : \PSX\DateTime\LocalDateTime
    {
        return $this->insertDate ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "insert_date" was provided');
    }
    public function toRecord() : \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('id', $this->id);
        $record->put('place', $this->place);
        $record->put('region', $this->region);
        $record->put('population', $this->population);
        $record->put('users', $this->users);
        $record->put('world_users', $this->worldUsers);
        $record->put('insert_date', $this->insertDate);
        return $record;
    }
    public function jsonSerialize() : object
    {
        return (object) $this->toRecord()->getAll();
    }
    public static function from(array|\ArrayAccess $data) : self
    {
        $row = new self();
        $row->id = isset($data['id']) && is_int($data['id']) ? $data['id'] : null;
        $row->place = isset($data['place']) && is_int($data['place']) ? $data['place'] : null;
        $row->region = isset($data['region']) && is_string($data['region']) ? $data['region'] : null;
        $row->population = isset($data['population']) && is_int($data['population']) ? $data['population'] : null;
        $row->users = isset($data['users']) && is_int($data['users']) ? $data['users'] : null;
        $row->worldUsers = isset($data['world_users']) && is_float($data['world_users']) ? $data['world_users'] : null;
        $row->insertDate = isset($data['insert_date']) && $data['insert_date'] instanceof \DateTimeInterface ? \PSX\DateTime\LocalDateTime::from($data['insert_date']) : null;
        return $row;
    }
}