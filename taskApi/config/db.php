<?php
$conn = new mysqli("localhost", "root", "", "taskapi");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}