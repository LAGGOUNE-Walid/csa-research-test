<?php

namespace App\Http;

use PDO;
use App\Models\User;
use LDAP\Result;

class Handler
{
    private PDO $databaseConnection;

    public static function withDatabaseConnection(PDO $databaseConnection): self
    {
        $instance = new self();
        $instance->databaseConnection = $databaseConnection;
        return $instance;
    }

    public function handle(string $requestMethod, string $requestUri): void
    {
        $uri = explode('/', $requestUri);
        if ($uri[0] === 'api' && $uri[1] === 'users') {
            $this->handleUsersApi($requestMethod, $uri, new User($this->databaseConnection));
        } else {
            new Response(["message" => "Route not found"], StatusCode::NOT_FOUND);
        }
    }

    public function handleUsersApi(string $requestMethod, array $uri, User $user): Response
    {
        if ($requestMethod == 'GET') {
            return new Response($user->getAll(), StatusCode::OK);
        } elseif ($requestMethod == 'POST') {
            return $this->handleCreateUserRequest(file_get_contents('php://input'), $user);
        }elseif($requestMethod == 'DELETE' OR $requestMethod == 'PUT') {
            if (!isset($uri[2])) {
                return new Response(["message" => "User ID required when deleting user"], StatusCode::BAD_REQUEST);
            }
            if ($requestMethod == 'PUT') {
                return $this->handleUpdateUserRequest(file_get_contents('php://input'), $user, $uri);
            }
            return $this->handleDeleteUserRequest(file_get_contents('php://input'), $user, $uri);
        }else {
            return new Response(["message" => "Method not allowed"], StatusCode::METHOD_NOT_ALLOWED);
        }
    }

    public function handleCreateUserRequest(string $data, User $user): Response
    {
        if (! json_validate($data)) {
            return new Response(["message" => "Invalid data format"], StatusCode::BAD_REQUEST);
        }
        $data = json_decode($data, true);

        if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            return new Response(["message" => "Invalid email format"], StatusCode::BAD_REQUEST);
        }

        if ($user->create($data['name'], $data['email'], $data['password'])) {
            return new Response(["message" => "User created"], StatusCode::CREATED);
        }
        return new Response(["message" => "Error when creating user, please try again"], StatusCode::BAD_REQUEST);
    }

    public function handleUpdateUserRequest(string $data, User $user, array $uri): Response
    {
        if (!isset($uri[2])) {
            return new Response(["message" => "User ID required when updating user"], StatusCode::BAD_REQUEST);
        }
        if (! json_validate($data)) {
            return new Response(["message" => "Invalid data format"], StatusCode::BAD_REQUEST);
        }
        $id = (int)$uri[2];
        $data = json_decode($data, true);

        if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            return new Response(["message" => "Invalid email format"], StatusCode::BAD_REQUEST);
        }

        if ($user->update($id, $data['name'], $data['email'])) {
            return new Response(["message" => "User updated"], StatusCode::CREATED);
        }
        return new Response(["message" => "Error when updating, please try again"], StatusCode::BAD_REQUEST);
    }

    public function handleDeleteUserRequest(string $data, User $user, array $uri): Response
    {
        $id = (int)$uri[2];
        if ($user->delete($id)) {
            return new Response(["message" => "User deleted"], StatusCode::CREATED);
        }
        return new Response(["message" => "Error when deleting, please try again"], StatusCode::BAD_REQUEST);
    }
}
