<?php

namespace LSM;

use Exception;
use Throwable;

class UserException extends Exception {

    protected $message = "";
    private string $string = "";
    protected $code;
    protected string $file = "";
    protected int $line;
    private array $trace = [];
    private ?Throwable $previous = null;
    public array $messages = [];

    public function __construct(string $message = "", array $messages = [], int $code = 0, ?Throwable $previous = null)
    {
        $this->messages = $messages;
        parent::__construct($message);
    }

    /**
     * @return array
     */
    public function getMessages() : array {
        return $this->messages;
    }
}