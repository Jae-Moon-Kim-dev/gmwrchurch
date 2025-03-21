<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = DB::select(
        'with recursive cte_menu(menu_id, menu_name, parent_menu_id, menu_order, depths, path_id, paths ) as (
            select menu_id
                , menu_name
                , parent_menu_id
                , menu_order
                , 0
                , cast(lpad(menu_id, 4, "0") as varchar(200))
                , cast(menu_name as varchar(200))
            from wr_menu
            where parent_menu_id is null
            union all
            select a.menu_id
                , a.menu_name
                , a.parent_menu_id
                , a.menu_order
                , b.depths + 1
                , concat(lpad(b.path_id, 4, "0"), " > ", lpad(a.menu_id, 4, "0"))
                , concat(b.paths, " > ", a.menu_name)
            from wr_menu a
            join cte_menu b on a.parent_menu_id = b.menu_id
        )
        select menu_id
            , menu_name
            , parent_menu_id
            , menu_order
            , depths
            , path_id
            , paths
        from cte_menu
        order by path_id');

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
