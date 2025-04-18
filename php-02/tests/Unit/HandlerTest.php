<?php
use PHPUnit\Framework\TestCase;
use App\Http\Handler;
use App\Models\User;

class HandlerTest extends TestCase
{
    public function testHandleUsersApiGetReturnsAllUsers()
    {
        $userMock = $this->createMock(User::class);
        $userMock->method('getAll')->willReturn([
            ['id' => 1, 'name' => 'walid', 'email' => 'walid@test.com']
        ]);

        $handler = Handler::withDatabaseConnection($this->createMock(PDO::class));
        
        ob_start();
        $handler->handleUsersApi('GET', ['api', 'users'], $userMock);
        $output = ob_get_clean();

        $this->assertJson($output);
        $this->assertStringContainsString('walid', $output);
    }

    public function testHandleReturnsRouteNotFound()
    {
        $handler = Handler::withDatabaseConnection($this->createMock(PDO::class));

        ob_start();
        $handler->handle('GET', 'api/foo');
        $output = ob_get_clean();

        $this->assertJson($output);
        $this->assertStringContainsString('Route not found', $output);
    }

    public function testHandleCreateUserWithInvalidJson()
    {
        $userMock = $this->createMock(User::class);

        $handler = Handler::withDatabaseConnection($this->createMock(PDO::class));

        $input = "not a json fake data";
        file_put_contents('php://memory', $input);


        ob_start();
        $handler->handleCreateUserRequest($input, $userMock);
        $output = ob_get_clean();

        $this->assertJson($output);
        $this->assertStringContainsString('Invalid data format', $output);
    }
}
