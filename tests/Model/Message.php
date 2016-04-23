<?php

namespace PSX\Project\Tests\Model;

/**
 * @Title("message")
 * @Description("Operation message")
 */
class Message
{
    /**
     * @Type("boolean")
     */
    protected $success;

    /**
     * @Type("string")
     */
    protected $message;

    public function getSuccess()
    {
        return $this->success;
    }

    public function setSuccess($success)
    {
        $this->success = $success;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }
}
