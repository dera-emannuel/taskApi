<?php
include __DIR__ . "/../controllers/taskController.php";

// creating instance of the controller
$taskController = new TaskController();
$request = $_SERVER['REQUEST_METHOD'];

switch ($request) {
    case "GET":
        $taskController->getTask();
        break;
    case "POST":
        $taskController->createTask();
        break;
    case "PUT":
        $taskController->updateTask();
        break;
    case "DELETE":
        $taskController->deleteTask();
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Invalid request method"]);
}
