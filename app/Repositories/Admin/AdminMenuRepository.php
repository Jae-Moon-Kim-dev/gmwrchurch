<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AdminMenuRepository {

    protected $logger;

    public function __construct() {
        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function getMenuList () {
        $this->logger->info('===getMenuList===');
        $menus = DB::select(
        'with recursive cte_menu(menu_id, menu_name, parent_menu_id, menu_order, depths, path_id, path_order_id, paths ) as (
            select menu_id
                , menu_name
                , parent_menu_id
                , menu_order
                , 0
                , cast(lpad(menu_id, 4, "0") as varchar(200))
                , cast(lpad(menu_order, 4, "0") as varchar(200))
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
                , concat(lpad(b.path_order_id, 4, "0"), " > ", lpad(a.menu_order, 4, "0"))
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
        order by path_order_id');

        return $menus;
    }

    public function getMenuById ($id) {
        $this->logger->info('===getMenuById===');
        $menu = collect(DB::select(
            'with recursive cte_menu(menu_id, menu_name, parent_menu_id, menu_type, menu_url, menu_order, visible_yn, depths, path_id, paths ) as (
                select menu_id
                    , menu_name
                    , parent_menu_id
                    , menu_type
                    , menu_url
                    , menu_order
                    , visible_yn
                    , 0
                    , cast(lpad(menu_id, 4, "0") as varchar(200))
                    , cast(menu_name as varchar(200))
                from wr_menu
                where parent_menu_id is null
                union all
                select a.menu_id
                    , a.menu_name
                    , a.parent_menu_id
                    , a.menu_type
                    , a.menu_url
                    , a.menu_order
                    , a.visible_yn
                    , b.depths + 1
                    , concat(lpad(b.path_id, 4, "0"), " > ", lpad(a.menu_id, 4, "0"))
                    , concat(b.paths, " > ", a.menu_name)
                from wr_menu a
                join cte_menu b on a.parent_menu_id = b.menu_id
            )
            select menu_id
                , menu_name
                , parent_menu_id
                , menu_type
                , menu_url
                , menu_order
                , visible_yn
                , depths
                , path_id
                , paths
            from cte_menu
           where menu_id = :menu_id', ["menu_id"=>$id]))->first();

            return $menu;
    }

    public function store( $menu ) {
        DB::insert('
            insert into wr_menu (
                parent_menu_id,
                menu_name,
                menu_url,
                visible_yn,
                menu_order,
                mem_id,
                create_date,
                modified_date
            ) values (
                :parent_menu_id,
                :menu_name,
                :menu_url,
                :visible_yn,
                (select max(a.menu_order)+1
                   from wr_menu a
                  where a.parent_menu_id is null),
                :mem_id,
                now(),
                now()
            )
        ', [
            "parent_menu_id"=> ( empty($menu->get('parent_menu_id')) ? null : $menu->get('parent_menu_id') ),
            "menu_name"=> $menu->get('menu_name'),
            "menu_url"=> $menu->get('menu_url'),
            "visible_yn"=> $menu->get('visible_yn'),
            "mem_id"=> Auth::user()->id
           ]);
    }

    public function update( $request, $id ) {
        DB::update('
            update wr_menu set
                menu_name= :menu_name,
                menu_url= :menu_url
            where menu_id = :menu_id
        ', ["menu_name"=> $request->input('menu_name'), "menu_url"=> $request->input('menu_url'), "menu_id"=> $id]);

    }
    public function updateOrder( $id, $menuOrder ) {
        DB::update('
            update wr_menu set
                menu_order= :menu_order
            where menu_id = :menu_id
        ', ["menu_order"=> $menuOrder, "menu_id"=> $id]);
    }

    public function destroy( $id ) {
        DB::delete('
            delete from wr_menu
            where menu_id = :menu_id
        ', ["menu_id"=> $id]);
    }
}
