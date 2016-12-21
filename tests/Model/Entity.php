<?php

namespace PSX\Project\Tests\Model;

/**
 * @Title("entity")
 * @Description("Represents an internet population entity")
 * @Required({"place", "region", "population", "users", "worldUsers"})
 */
class Entity
{
    /**
     * @Type("integer")
     * @Description("Unique id for each entry")
     */
    protected $id;

    /**
     * @Type("integer")
     * @Minimum(1)
     * @Maximum(64)
     * @Description("Position in the top list")
     */
    protected $place;

    /**
     * @Type("string")
     * @MinLength(3)
     * @MaxLength(64)
     * @Pattern("[A-z]+")
     * @Description("Name of the region")
     */
    protected $region;

    /**
     * @Type("integer")
     * @Description("Complete number of population")
     */
    protected $population;

    /**
     * @Type("integer")
     * @Description("Number of internet users")
     */
    protected $users;

    /**
     * @Type("number")
     * @Description("Percentage users of the world")
     */
    protected $worldUsers;

    /**
     * @Type("string")
     * @Format("date-time")
     * @Description("Date when the entity was created")
     */
    protected $datetime;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getPlace()
    {
        return $this->place;
    }

    public function setPlace($place)
    {
        $this->place = $place;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function setRegion($region)
    {
        $this->region = $region;
    }

    public function getPopulation()
    {
        return $this->population;
    }

    public function setPopulation($population)
    {
        $this->population = $population;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function setUsers($users)
    {
        $this->users = $users;
    }

    public function getWorldUsers()
    {
        return $this->worldUsers;
    }

    public function setWorldUsers($worldUsers)
    {
        $this->worldUsers = $worldUsers;
    }

    public function getDatetime()
    {
        return $this->datetime;
    }

    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }
}
