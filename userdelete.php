<?php
require('ibsng.class.php');

$username = "";
$password = "";
$ip = "";

$ibsngClient = new IBSng($username , $password , $ip);

$filename = 'info.txt';
$user_ids_file = file($filename);

foreach($user_ids_file as $line) {
    $ibsngClient -> removeUser($line);
}
?>
