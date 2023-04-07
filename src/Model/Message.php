<?php

declare(strict_types = 1);

namespace App\Model;


class Message implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?bool $success = null;
    protected ?string $message = null;
    protected ?int $id = null;
    public function setSuccess(?bool $success) : void
    {
        $this->success = $success;
    }
    public function getSuccess() : ?bool
    {
        return $this->success;
    }
    public function setMessage(?string $message) : void
    {
        $this->message = $message;
    }
    public function getMessage() : ?string
    {
        return $this->message;
    }
    public function setId(?int $id) : void
    {
        $this->id = $id;
    }
    public function getId() : ?int
    {
        return $this->id;
    }
    public function toRecord() : \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('success', $this->success);
        $record->put('message', $this->message);
        $record->put('id', $this->id);
        return $record;
    }
    public function jsonSerialize() : object
    {
        return (object) $this->toRecord()->getAll();
    }
}

