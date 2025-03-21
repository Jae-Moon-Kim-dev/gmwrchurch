<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Facades\DB;

class AdminMenuRepository {
    public function getMenuList () {
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

        return $menus;
    }
}
