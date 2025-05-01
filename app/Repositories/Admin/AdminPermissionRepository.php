<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AdminPermissionRepository {

    protected $logger;

    public function __construct() {
        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function getMenuRoleList () {
        $this->logger->info('===getMenuRoleList===');
        $menuRoles = DB::select(
        'select aa.parent_menu_id
              , aa.parent_menu_name
              , aa.menu_id
              , aa.menu_name
              , aa.role_id
              , aa.role_name
              , bb.read_yn
              , bb.write_yn
              , bb.admin_yn
           from (select a.menu_id
 					  , a.menu_name
 					  , a.parent_menu_id
 					  , (select x.menu_name
                         from wr_menu x
                        where x.menu_id = a.parent_menu_id) as parent_menu_name
                      , b.role_id
                      , b.role_name
           from (with recursive cte_menu(menu_id, menu_name, parent_menu_id, menu_order, depths, path_id, path_order_id, paths ) as (
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
		           select c.menu_id
		                , c.menu_name
		                , c.parent_menu_id
		                , c.menu_order
		                , d.depths + 1
		                , concat(lpad(d.path_id, 4, "0"), " > ", lpad(c.menu_id, 4, "0"))
		                , concat(lpad(d.path_order_id, 4, "0"), " > ", lpad(c.menu_order, 4, "0"))
		                , concat(d.paths, " > ", c.menu_name)
		            from wr_menu c
		            join cte_menu d on c.parent_menu_id = d.menu_id
		        )
		       select menu_id
		            , menu_name
		            , parent_menu_id
		            , menu_order
		        from cte_menu
		        order by path_order_id) a
             cross join ( select b0.role_id
                               , b0.role_name
                            from wr_role b0
                           where b0.role_id not in (1,2) ) b
                  where a.parent_menu_id is not null) aa
      left join (select a.menu_id
                      , a.role_id
                      , a.read_yn
                      , a.write_yn
                      , a.admin_yn
                   from wr_menu_role a) bb on aa.menu_id = bb.menu_id
                                          and aa.role_id = bb.role_id');

        return $menuRoles;
    }

    public function getMenuRole ( $menu_id, $role_id ) {
        $this->logger->info('===getMenuRole===');

        $menuRole = DB::select('
            select menu_id
                 , role_id
              from wr_menu_role
             where menu_id = :menu_id
               and role_id = :role_id
        ', ['menu_id'=>$menu_id, 'role_id'=>$role_id]);

        return $menuRole;
    }

    public function insertMenuRole ( $menuRole ) {
        $this->logger->info('===insertMenuRole===');

        DB::insert('
            insert into wr_menu_role (
                menu_id,
                role_id,
                read_yn,
                write_yn,
                admin_yn,
                mem_id,
                create_date,
                modified_date
            ) values (
                :menu_id,
                :role_id,
                :read_yn,
                :write_yn,
                :admin_yn,
                :mem_id,
                now(),
                now()
            )
        ',[
            'menu_id'=>$menuRole->menu_id,
            'role_id'=>$menuRole->role_id,
            'read_yn'=>$menuRole->read_yn,
            'write_yn'=>$menuRole->write_yn,
            'admin_yn'=>$menuRole->admin_yn,
            'mem_id'=>Auth::user()->id
        ]);
    }

    public function updateMenuRole ( $menuRole ) {
        $this->logger->info('===updateMenuRole===');
        DB::update('
            update wr_menu_role set
                read_yn = :read_yn,
                write_yn = :write_yn,
                admin_yn = :admin_yn,
                mem_id = :mem_id,
                modified_date = now()
            where menu_id = :menu_id
              and role_id = :role_id
        ', [
            'menu_id'=>$menuRole->menu_id,
            'role_id'=>$menuRole->role_id,
            'read_yn'=>$menuRole->read_yn,
            'write_yn'=>$menuRole->write_yn,
            'admin_yn'=>$menuRole->admin_yn,
            'mem_id'=>Auth::user()->id
        ]);
    }
}
