<?php

$host = 'localhost';
$dbname = 'fus';  // Replace with your actual database name
$username = 'root';  // Replace with your MySQL username
$password = '';  // Replace with your MySQL password

try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
  // Set the PDO error mode to exception
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

session_start();
