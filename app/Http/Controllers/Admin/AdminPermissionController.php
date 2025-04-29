<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminPermissionService;
use Illuminate\Http\Request;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class AdminPermissionController extends Controller
{
    protected $adminPermissionService;
    protected $logger;

    public function __construct(AdminPermissionService $adminPermissionService) {
        $this->adminPermissionService = $adminPermissionService;

        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function index()
    {
        $this->logger->info('===index===');
        $menus = $this->adminPermissionService->getMenuRoleList();

        if ( !$menus ) {
            return response()->json(['success'=>false, 'message'=>'No Data'], 401);
        } else {
            return response()->json(['success'=>true, 'data'=>$menus], 200);
        }

    }
}
