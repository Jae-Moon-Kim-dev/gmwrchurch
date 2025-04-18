<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminRoleService;
use Illuminate\Http\Request;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class AdminRoleController extends Controller
{
    protected $adminRoleService;
    protected $logger;

    public function __construct(AdminRoleService $adminRoleService) {
        $this->adminRoleService = $adminRoleService;

        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function index() {

        $this->logger->info('===index Start===');
        
        return $this->adminRoleService->getRoleList();
    }
}
