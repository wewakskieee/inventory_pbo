<?php
namespace App\Services;

interface ValidatorInterface {
    public function validate(array $data): array;
}
