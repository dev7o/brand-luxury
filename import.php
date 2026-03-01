<?php
$host = 'sql206.ezyro.com';
$user = 'ezyro_41274331';
$pass = 'f11acf9f443';

$pdo = new PDO("mysql:host=$host;dbname=ezyro_41274331_lamasa12;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$sql = file_get_contents(__DIR__ . '/brand.sql');

// execute the SQL
$pdo->exec($sql);
echo "Import successful via PHP.";
