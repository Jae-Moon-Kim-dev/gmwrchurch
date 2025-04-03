<?php

namespace App\Repositories\Auth;

use Illuminate\Support\Facades\DB;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AuthRepository {

    protected $logger;

    public function __construct() {
        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log'), Logger::INFO));
    }

    public function getUserById ($id) {
        $this->logger->info('===getUserById===');
        $user = collect(DB::select(
            'select id
                  , name
                  , email
               from users
              where email = :id', ["id"=>$id]))->first();
    
        return $user;
    }
}
