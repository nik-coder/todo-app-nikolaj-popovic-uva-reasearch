<?php
header('Content-Type: application/json');

$filePath = '../data/tasks.json';

// Read tasks from JSON file
function readTasks() {
    global $filePath;
    if (file_exists($filePath)) {
        $data = file_get_contents($filePath);
        return json_decode($data, true);
    }
    return [];
}

// Write tasks to JSON file
function writeTasks($tasks) {
    global $filePath;
    file_put_contents($filePath, json_encode($tasks, JSON_PRETTY_PRINT));
}

// Handle different request methods
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        echo json_encode(readTasks());
        break;
    case 'POST':
        $tasks = readTasks();
        $newTask = json_decode(file_get_contents('php://input'), true);
        $newTask['id'] = uniqid();
        $tasks[] = $newTask;
        writeTasks($tasks);
        echo json_encode($newTask);
        break;
    case 'PUT':
        $tasks = readTasks();
        $updatedTask = json_decode(file_get_contents('php://input'), true);
        foreach ($tasks as &$task) {
            if ($task['id'] === $updatedTask['id']) {
                $task['task'] = $updatedTask['task'];
                break;
            }
        }
        writeTasks($tasks);
        echo json_encode($updatedTask);
        break;
    case 'DELETE':
        $tasks = readTasks();
        $deleteId = json_decode(file_get_contents('php://input'), true)['id'];
        $tasks = array_filter($tasks, fn($task) => $task['id'] !== $deleteId);
        writeTasks(array_values($tasks));
        echo json_encode(['status' => 'success']);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        break;
}
?>