<?php

$servername = "localhost";
$username = "kinguser";
$password = "king4";
$schema = "taskdb";
$conn = new PDO("mysql:host=$servername;dbname=$schema", $username, $password);

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$stmt = $conn->prepare("SELECT * FROM movies");
$result = $stmt->execute();
$result = $stmt->fetchAll();
print_r($result);
?>