<?php

namespace App\Table\Generated;

class PopulationRow extends \PSX\Record\Record
{
    public function setId(int $id) : void
    {
        $this->setProperty('id', $id);
    }
    public function getId() : int
    {
        return $this->getProperty('id');
    }
    public function setPlace(int $place) : void
    {
        $this->setProperty('place', $place);
    }
    public function getPlace() : int
    {
        return $this->getProperty('place');
    }
    public function setRegion(string $region) : void
    {
        $this->setProperty('region', $region);
    }
    public function getRegion() : string
    {
        return $this->getProperty('region');
    }
    public function setPopulation(int $population) : void
    {
        $this->setProperty('population', $population);
    }
    public function getPopulation() : int
    {
        return $this->getProperty('population');
    }
    public function setUsers(int $users) : void
    {
        $this->setProperty('users', $users);
    }
    public function getUsers() : int
    {
        return $this->getProperty('users');
    }
    public function setWorldUsers(float $worldUsers) : void
    {
        $this->setProperty('world_users', $worldUsers);
    }
    public function getWorldUsers() : float
    {
        return $this->getProperty('world_users');
    }
    public function setInsertDate(\DateTime $insertDate) : void
    {
        $this->setProperty('insert_date', $insertDate);
    }
    public function getInsertDate() : \DateTime
    {
        return $this->getProperty('insert_date');
    }
}