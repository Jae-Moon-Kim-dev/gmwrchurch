<?php

namespace App\Services\Auth;

use App\Repositories\Auth\AuthRepository;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AuthService {
    protected $authRepository;
    protected $logger;

    public function __construct(AuthRepository $authRepository) {
        $this->authRepository = $authRepository;

        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log'), Logger::INFO));
    }

    public function getUserById($id) {
        $this->logger->info('===getUserById===');

        return $this->authRepository->getUserById($id);
    }
}
