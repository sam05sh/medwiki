<?php
// config.php
$db_host = 'localhost';
$db_user = 'medwiki';
$db_pass = '';
$db_name = 'my_medwiki';
$admin_password = 'passwordAdmin'; // CAMBIA QUESTA PASSWORD

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_errno) { die("Errore DB: " . $conn->connect_error); }
$conn->set_charset("utf8mb4");
?>
