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
        return $this->id;
    }
    public function setPlace(int $place) : void
    {
        $this->place = $place;
    }
    public function getPlace() : int
    {
        return $this->place;
    }
    public function setRegion(string $region) : void
    {
        $this->region = $region;
    }
    public function getRegion() : string
    {
        return $this->region;
    }
    public function setPopulation(int $population) : void
    {
        $this->population = $population;
    }
    public function getPopulation() : int
    {
        return $this->population;
    }
    public function setUsers(int $users) : void
    {
        $this->users = $users;
    }
    public function getUsers() : int
    {
        return $this->users;
    }
    public function setWorldUsers(float $worldUsers) : void
    {
        $this->worldUsers = $worldUsers;
    }
    public function getWorldUsers() : float
    {
        return $this->worldUsers;
    }
    public function setInsertDate(\PSX\DateTime\LocalDateTime $insertDate) : void
    {
        $this->insertDate = $insertDate;
    }
    public function getInsertDate() : \PSX\DateTime\LocalDateTime
    {
        return $this->insertDate;
    }
    public function toRecord() : \PSX\Record\RecordInterface
    {
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
        $row->id = $data['id'] ?? null;
        $row->place = $data['place'] ?? null;
        $row->region = $data['region'] ?? null;
        $row->population = $data['population'] ?? null;
        $row->users = $data['users'] ?? null;
        $row->worldUsers = $data['world_users'] ?? null;
        $row->insertDate = isset($data['insert_date']) ? \PSX\DateTime\LocalDateTime::from($data['insert_date']) : null;
        return $row;
    }
}