<?php

use PHPUnit\Framework\TestCase;
use App\Models\User;

class UserTest extends TestCase
{
    private PDO $pdo;
    private User $user;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->pdo->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                password TEXT NOT NULL
            )
        ");

        $this->user = new User($this->pdo);
    }

    public function testCreateUser()
    {
        $result = $this->user->create('Walid Test', 'test@example.com', 'secret123');
        $this->assertTrue($result);

        $stmt = $this->pdo->query("SELECT * FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(1, $users);
        $this->assertEquals('Walid Test', $users[0]['name']);
        $this->assertEquals('test@example.com', $users[0]['email']);
        $this->assertTrue(password_verify('secret123', $users[0]['password']));
    }

    public function testUpdateUser()
    {
        $this->user->create('Walid', 'walid@example.com', 'password');
        $this->user->update(1, 'Mohamed', 'mohamed@example.com');

        $stmt = $this->pdo->query("SELECT * FROM users WHERE id = 1");
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals('Mohamed', $user['name']);
        $this->assertEquals('mohamed@example.com', $user['email']);
    }

    public function testDeleteUser()
    {
        $this->user->create('walid', 'walid@example.com', '1234');
        $deleted = $this->user->delete(1);

        $this->assertTrue($deleted);

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM users");
        $count = $stmt->fetchColumn();

        $this->assertEquals(0, $count);
    }

    public function testGetAll()
    {
        $this->user->create('A', 'a@example.com', 'a');
        $this->user->create('B', 'b@example.com', 'b');

        $users = $this->user->getAll();
        $this->assertCount(2, $users);
        $this->assertEquals('A', $users[0]['name']);
        $this->assertEquals('B', $users[1]['name']);
    }
}
