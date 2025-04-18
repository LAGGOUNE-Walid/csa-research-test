<?php

namespace App\Http;

use App\Http\StatusCode;


class Response
{
    public function __construct(
        private array $content,
        private StatusCode $statusCode
    ) {
        http_response_code($statusCode->value);
        header('Content-Type: application/json');
        echo json_encode($content);
        exit;
    }
}
