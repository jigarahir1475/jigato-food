<?php
namespace PHPMailer\PHPMailer;

use Exception as BaseException;

class Exception extends BaseException
{
    protected $message = '';

    public function __construct($message = '')
    {
        $this->message = $message;
    }

    public function errorMessage()
    {
        return $this->message;
    }
}
?>
