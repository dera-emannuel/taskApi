<?php

include __DIR__ . "/../models/task.php";

class TaskController {
    private $default_user_id;

    public function __construct(){
        $task = new Task();
        $this->default_user_id = $task->getFirstUserId();
    }

    private function resolveUserId($requestedUserId = null){
        $task = new Task();

        if ($requestedUserId !== null) {
            $requestedUserId = (int)$requestedUserId;
            if ($requestedUserId <= 0 || !$task->userExists($requestedUserId)) {
                http_response_code(400);
                echo json_encode(["message" => "Invalid user_id. The specified user does not exist."]);
                return null;
            }
            return $requestedUserId;
        }

        if ($this->default_user_id === null) {
            http_response_code(400);
            echo json_encode([
                "message" => "No users found. Create a user first before creating tasks."
            ]);
            return null;
        }

        return $this->default_user_id;
    }

    // get all tasks for the user
    public function getTask(){
        $user_id = $this->resolveUserId($_GET['user_id'] ?? null);
        if ($user_id === null) {
            return;
        }

        $task = new Task();
        $tasks = $task->getTasks($user_id);
        echo json_encode($tasks);
    }

    // create a new task
    public function createTask(){
        $data = json_decode(file_get_contents("php://input"), true);

        // Accept both {"title":"...","description":"..."} and [{"title":"...","description":"..."}]
        if (is_array($data) && isset($data[0]) && is_array($data[0])) {
            $data = $data[0];
        }

        $user_id = $this->resolveUserId($data['user_id'] ?? null);
        if ($user_id === null) {
            return;
        }

        if (!is_array($data) || !isset($data['title'], $data['description']) || trim((string)$data['title']) === '' || trim((string)$data['description']) === '') {
            http_response_code(400);
            echo json_encode([
                "message" => "Invalid payload. 'title' and 'description' are required.",
                "expected_examples" => [
                    ["title" => "My Task", "description" => "Backend class"],
                    [["title" => "My Task", "description" => "Backend class"]]
                ]
            ]);
            return;
        }

        $task = new Task();
        $task->createTask($user_id, $data['title'], $data['description']);
        echo json_encode(["message" => "Task created successfully", "user_id" => $user_id]);
    }

    // update an existing task
    public function updateTask(){
        // get all json data from the request body
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data) || !isset($data['id'], $data['title'], $data['description'])) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid payload. 'id', 'title' and 'description' are required."]);
            return;
        }

        $user_id = $this->resolveUserId($data['user_id'] ?? null);
        if ($user_id === null) {
            return;
        }

        $task = new Task();
        $task->updateTask((int)$data['id'], $user_id, $data['title'], $data['description']);
        echo json_encode(["message" => "Task updated successfully"]);
    }

    // Delete a task
    public function deleteTask(){
        // get task id from the url (?id=1)
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["message" => "Missing task id"]);
            return;
        }

        $user_id = $this->resolveUserId($_GET['user_id'] ?? null);
        if ($user_id === null) {
            return;
        }

        $task_id = (int)$_GET['id'];
        $task = new Task();
        $task->deleteTask($task_id, $user_id);
        echo json_encode(["message" => "Task deleted successfully"]);
    }
}
