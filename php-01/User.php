<?php


class User
{

    public function __construct(
        private string $name,
        private string $email,
        private string $password,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function setPassword(string $password, string|int|null $algo): void
    {
        $this->password = password_hash($password, $algo);
    }

    public static function getAllUsers(): array
    {
        $users = [];
        try {
            // Note: 
            //     - Database credentials must be stored at safe place like .env files or secrets store 
            //     - It's better to use connection pool instead of creating new PDO connection when getting the users
            $pdo = new PDO("mysql:host=localhost;dbname=myusersdatabase", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // // Note: 
            //     - better to use pagination here
            $stmt = $pdo->query("SELECT name, email, password FROM users");

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $user = new User($row['name'], $row['email'], $row['password']);
                $users[] = $user;
            }
        } catch (PDOException $e) {
            echo "DB error: " . $e->getMessage();
        }

        return $users;
    }
}
