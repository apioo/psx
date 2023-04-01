<?php

declare(strict_types = 1);

namespace App\Model;


class Welcome implements \JsonSerializable
{
    protected ?string $message = null;
    protected ?string $url = null;
    protected ?string $version = null;
    public function setMessage(?string $message) : void
    {
        $this->message = $message;
    }
    public function getMessage() : ?string
    {
        return $this->message;
    }
    public function setUrl(?string $url) : void
    {
        $this->url = $url;
    }
    public function getUrl() : ?string
    {
        return $this->url;
    }
    public function setVersion(?string $version) : void
    {
        $this->version = $version;
    }
    public function getVersion() : ?string
    {
        return $this->version;
    }
    public function jsonSerialize() : object
    {
        return (object) array_filter(array('message' => $this->message, 'url' => $this->url, 'version' => $this->version), static function ($value) : bool {
            return $value !== null;
        });
    }
}

