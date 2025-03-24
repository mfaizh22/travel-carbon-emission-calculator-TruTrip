<?php

namespace App\Domains\Users\Services;

interface AuthServiceInterface
{
    public function login($data);
    public function register($data);
}
