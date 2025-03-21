<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AdminMenuService;

class AdminMenuController extends Controller
{
    protected $adminMenuService;

    public function __construct(AdminMenuService $adminMenuService) {
        $this->adminMenuService = $adminMenuService;
    }

    /**
     * Display a listing of the resource.
     */
    public function getMenuList()
    {
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
