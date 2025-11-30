<?php
namespace App\Views;

class ResponseFormatter {
    public static function success($data, string $message = 'Success', int $code = 200): array {
        return [
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
    }
    
    public static function error(string $message, int $code = 500, $errors = null): array {
        return [
            'success' => false,
            'code' => $code,
            'message' => $message,
            'errors' => $errors
        ];
    }
}
