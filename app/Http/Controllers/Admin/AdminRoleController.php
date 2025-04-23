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
        $roles = $this->adminRoleService->getRoleList();
        
        if ( !$roles ) {
            return response()->json(['success'=>false, 'message'=>'No Data'], 401);
        } else {
            return response()->json(['success'=>true, 'data'=>$roles], 200);
        }
    }

    public function store(Request $request) {
        $this->logger->info('===store Start===');
        
        $this->adminRoleService->store($request);
    }

    public function update(Request $request, string $id) {
        $this->adminRoleService->update($request, $id);
    }

    public function destroy($id) {
        $this->logger->info('===destroy Start===');

        $this->adminRoleService->destroy($id);
    }
}
