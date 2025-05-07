<?php

namespace App\Repositories\Auth;

use Illuminate\Support\Facades\DB;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AuthRepository {

    protected $logger;

    public function __construct() {
        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
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

    public function getMenus($id) {
        $this->logger->info('===getMenus===');
        $param = [];
        $sql = 'with recursive cte_menu(menu_id, menu_name, menu_url, parent_menu_id, menu_order, depths, path_id, path_order_id, paths, visible_yn ) as (
                select menu_id
                     , menu_name
                     , menu_url
                     , parent_menu_id
                     , menu_order
                     , 0
                     , cast(lpad(menu_id, 4, "0") as varchar(200))
                     , cast(lpad(menu_order, 4, "0") as varchar(200))
                     , cast(menu_name as varchar(200))
                     , visible_yn
                  from wr_menu
                 where parent_menu_id is null
                 union all
                select a.menu_id
                     , a.menu_name
                     , a.menu_url
                     , a.parent_menu_id
                     , a.menu_order
                     , b.depths + 1
                     , concat(lpad(b.path_id, 4, "0"), " > ", lpad(a.menu_id, 4, "0"))
                     , concat(lpad(b.path_order_id, 4, "0"), " > ", lpad(a.menu_order, 4, "0"))
                     , concat(b.paths, " > ", a.menu_name)
                     , a.visible_yn
                  from wr_menu a
                  join cte_menu b on a.parent_menu_id = b.menu_id
            )
            select menu_id
                 , menu_name
                 , menu_url
                 , parent_menu_id
                 , menu_order
                 , depths
                 , path_id
                 , paths
              from cte_menu
             where visible_yn = "Y"
          ';

        if ( !empty( $id ) ) {
            $sql.= ' and (menu_id = :parent_menu_id1 or parent_menu_id = :parent_menu_id2)';
            $param['parent_menu_id1'] = $id;
            $param['parent_menu_id2'] = $id;
        }

        $sql.= ' order by path_order_id';

        $menus = DB::select($sql, $param);

        return $menus;
    }
}
