<?php

namespace App\Models;

use PDO;

class User {

    public function __construct(
        private PDO $pdo
    )
    {
    }

    public function create(string $name, string $email, string $password): bool
    {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $email, $hashed]);
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("SELECT id, name, email FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(int $id, string $name, string $email): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        return $stmt->execute([$name, $email, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
}