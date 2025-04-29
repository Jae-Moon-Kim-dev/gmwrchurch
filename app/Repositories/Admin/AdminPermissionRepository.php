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
           from (select a.parent_menu_id 
                      , (select x.menu_name
                           from wr_menu x
                          where x.menu_id = a.parent_menu_id) as parent_menu_name
                      , a.menu_id 
                      , a.menu_name 
                      , b.role_id
                      , b.role_name
                   from wr_menu a
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
}
