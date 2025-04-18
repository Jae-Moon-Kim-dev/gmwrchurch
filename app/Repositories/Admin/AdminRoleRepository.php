<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AdminRoleRepository {

    protected $logger;

    public function __construct() {
        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function getRoleList () {
        $this->logger->info('===getRoleList===');
        $roles = DB::select(
        'select role_id
              , role_name
              , edit_yn
           from wr_role');

        return $roles;
    }
}
