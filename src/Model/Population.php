<?php

declare(strict_types = 1);

namespace App\Model;


class Population implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?int $id = null;
    protected ?int $place = null;
    protected ?string $region = null;
    protected ?int $population = null;
    protected ?int $users = null;
    protected ?float $worldUsers = null;
    protected ?\PSX\DateTime\LocalDateTime $insertDate = null;
    public function setId(?int $id) : void
    {
        $this->id = $id;
    }
    public function getId() : ?int
    {
        return $this->id;
    }
    public function setPlace(?int $place) : void
    {
        $this->place = $place;
    }
    public function getPlace() : ?int
    {
        return $this->place;
    }
    public function setRegion(?string $region) : void
    {
        $this->region = $region;
    }
    public function getRegion() : ?string
    {
        return $this->region;
    }
    public function setPopulation(?int $population) : void
    {
        $this->population = $population;
    }
    public function getPopulation() : ?int
    {
        return $this->population;
    }
    public function setUsers(?int $users) : void
    {
        $this->users = $users;
    }
    public function getUsers() : ?int
    {
        return $this->users;
    }
    public function setWorldUsers(?float $worldUsers) : void
    {
        $this->worldUsers = $worldUsers;
    }
    public function getWorldUsers() : ?float
    {
        return $this->worldUsers;
    }
    public function setInsertDate(?\PSX\DateTime\LocalDateTime $insertDate) : void
    {
        $this->insertDate = $insertDate;
    }
    public function getInsertDate() : ?\PSX\DateTime\LocalDateTime
    {
        return $this->insertDate;
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
        $record->put('worldUsers', $this->worldUsers);
        $record->put('insertDate', $this->insertDate);
        return $record;
    }
    public function jsonSerialize() : object
    {
        return (object) $this->toRecord()->getAll();
    }
}

