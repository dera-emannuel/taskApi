<?php
// get all tasks for the user
include __DIR__ . "/../config/db.php";

class Task {
    public function getTasks($user_id){
        global $conn;

        // query to get all tasks for the user
        $stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // get the first existing user id to satisfy foreign key constraints
    public function getFirstUserId(){
        global $conn;

        $result = $conn->query("SELECT id FROM users ORDER BY id ASC LIMIT 1");
        if (!$result || $result->num_rows === 0) {
            return null;
        }

        $row = $result->fetch_assoc();
        return (int)$row['id'];
    }

    public function userExists($user_id){
        global $conn;

        $stmt = $conn->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result && $result->num_rows > 0;
    }

    // create a new task
    public function createTask($user_id, $title, $description){
        global $conn;

        // query to insert a new task
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $title, $description);
        $stmt->execute();
    }

    // update an existing task
    public function updateTask($id, $user_id, $title, $description){
        global $conn;

        // query to update a task
        $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssii", $title, $description, $id, $user_id);
        $stmt->execute();
    }

    // delete a task
    public function deleteTask($id, $user_id){
        global $conn;

        // query to delete a task
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
    }
}
