<?php

header("content-type: application/json");
$request = $_SERVER['REQUEST_URI'];
include './routes/api.php';