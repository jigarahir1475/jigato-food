<?php
/**
 * PHPMailer - PHP email creation and transport class.
 * @package PHPMailer
 * @see https://github.com/PHPMailer/PHPMailer
 */

namespace PHPMailer\PHPMailer;

use Exception;

class PHPMailer
{
    public $Priority = 3;
    public $CharSet = 'UTF-8';
    public $ContentType = 'text/plain';
    public $Encoding = '8bit';
    public $ErrorInfo = '';
    public $From = '';
    public $FromName = '';
    public $Sender = '';
    public $Subject = '';
    public $Body = '';
    public $AltBody = '';
    public $Ical = '';
    public $WordWrap = 0;
    public $Mailer = 'smtp';
    public $Sendmail = '/usr/sbin/sendmail';
    public $Timeout = 300;
    public $SMTPDebug = 0;
    public $SMTPAuth = true;
    public $SMTPSecure = '';
    public $Port = 25;
    public $Host = '';
    public $Username = '';
    public $Password = '';
    public $SMTPOptions = [];
    public $smtp;
    public $to = [];
    public $ReplyTo = [];
    public $all_recipients = [];
    public $attachments = [];

    public function __construct($exceptions = null)
    {
        $this->exceptions = ($exceptions === true);
    }

    public function isSMTP()
    {
        $this->Mailer = 'smtp';
    }

    public function setFrom($address, $name = '')
    {
        $this->From = $address;
        $this->FromName = $name;
    }

    public function addAddress($address, $name = '')
    {
        $this->to[] = [$address, $name];
    }

    public function isHTML($isHtml = true)
    {
        $this->ContentType = $isHtml ? 'text/html' : 'text/plain';
    }

    public function send()
    {
        if ($this->Mailer === 'smtp') {
            $this->smtp = new SMTP();
            $this->smtp->do_connect($this->Host, $this->Port);
            $this->smtp->hello(gethostname());
            if ($this->SMTPAuth) {
                $this->smtp->authenticate($this->Username, $this->Password);
            }

            foreach ($this->to as $to) {
                $this->smtp->mail($this->From);
                $this->smtp->recipient($to[0]);
            }

            $header = "From: {$this->FromName} <{$this->From}>\r\n";
            $header .= "Subject: {$this->Subject}\r\n";
            $header .= "Content-Type: {$this->ContentType}; charset={$this->CharSet}\r\n";

            $body = $this->Body;
            $this->smtp->data($header . "\r\n" . $body);
            $this->smtp->quit();
            $this->smtp->close();
            return true;
        } else {
            return mail($this->to[0][0], $this->Subject, $this->Body, "From: {$this->From}");
        }
    }
}
?>
