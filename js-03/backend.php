<?php
$db = new PDO('sqlite:users.db');

// migration just for testing
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL
)");

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['name'], $input['email']) || !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['message' => 'Invalid input']);
    exit;
}

$stmt = $db->prepare("INSERT INTO users (name, email) VALUES (:name, :email)");
$stmt->bindValue(':name', $input['name']);
$stmt->bindValue(':email', $input['email']);
$stmt->execute();

echo json_encode(['message' => 'User saved successfully!']);
