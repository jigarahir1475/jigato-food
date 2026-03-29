<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
$host = 'smtp.gmail.com';
$port = 587;
$socket = @fsockopen($host, $port, $errno, $errstr, 10);
if ($socket) { echo "✅ Connected to $host:$port"; fclose($socket); }
else { echo "❌ Cannot connect to $host:$port — $errstr ($errno)"; }
?>
