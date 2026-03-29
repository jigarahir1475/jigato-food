<?php
namespace PHPMailer\PHPMailer;

class SMTP
{
    public $smtp_conn;
    public $error = '';

    public function do_connect($host, $port)
    {
        $this->smtp_conn = fsockopen($host, $port, $errno, $errstr, 30);
        if (!$this->smtp_conn) {
            $this->error = "Failed to connect: $errstr ($errno)";
        }
    }

    public function hello($hostname)
    {
        fputs($this->smtp_conn, "EHLO $hostname\r\n");
        $this->get_lines();
    }

    public function authenticate($username, $password)
    {
        fputs($this->smtp_conn, "AUTH LOGIN\r\n");
        $this->get_lines();
        fputs($this->smtp_conn, base64_encode($username) . "\r\n");
        $this->get_lines();
        fputs($this->smtp_conn, base64_encode($password) . "\r\n");
        $this->get_lines();
    }

    public function mail($from)
    {
        fputs($this->smtp_conn, "MAIL FROM:<$from>\r\n");
        $this->get_lines();
    }

    public function recipient($to)
    {
        fputs($this->smtp_conn, "RCPT TO:<$to>\r\n");
        $this->get_lines();
    }

    public function data($data)
    {
        fputs($this->smtp_conn, "DATA\r\n");
        $this->get_lines();
        fputs($this->smtp_conn, $data . "\r\n.\r\n");
        $this->get_lines();
    }

    public function quit()
    {
        fputs($this->smtp_conn, "QUIT\r\n");
        $this->get_lines();
    }

    public function close()
    {
        fclose($this->smtp_conn);
    }

    private function get_lines()
    {
        $data = "";
        while ($str = fgets($this->smtp_conn, 515)) {
            $data .= $str;
            if (substr($str, 3, 1) == " ") {
                break;
            }
        }
        return $data;
    }
}
?>
