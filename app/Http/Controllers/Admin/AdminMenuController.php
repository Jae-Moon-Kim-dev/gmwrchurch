<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AdminMenuService;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AdminMenuController extends Controller
{
    protected $adminMenuService;
    protected $logger;

    public function __construct(AdminMenuService $adminMenuService) {
        $this->adminMenuService = $adminMenuService;

        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->logger->info('===index===');
        $menus = $this->adminMenuService->getMenuList();

        if ( !$menus ) {
            return response()->json(['success'=>false, 'message'=>'No Data'], 401);
        } else {
            return response()->json(['success'=>true, 'data'=>$menus], 200);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->logger->info('===store===');
        $menus = $request->collect();

        $this->adminMenuService->store($menus);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->logger->info('===show===');
        $menu = $this->adminMenuService->getMenuById($id);

        if ( !$menu ) {
            return response()->json(['success'=>false, 'message'=>'No Data'], 401);
        } else {
            return response()->json(['success'=>true, 'data'=>$menu], 200);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->logger->info('===update===');
        $menus = $request->collect();

        $this->logger->info('===update==='.print_r(json_decode($menus, true)));
        $this->logger->info('===update===id => '.$id);

        $this->adminMenuService->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->logger->info('===destroy===');
        $this->logger->info('===destroy===id => '.$id);

        $this->adminMenuService->destroy($id);
    }
}
