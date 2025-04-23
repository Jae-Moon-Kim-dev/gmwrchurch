<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AdminRoleRepository {

    protected $logger;

    public function __construct() {
        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function getRoleList () {
        $this->logger->info('===getRoleList===');
        $roles = DB::select(
        'select role_id
              , role_name
              , description
              , edit_yn
           from wr_role');

        return $roles;
    }

    public function store( $role ) {
        $this->logger->info('===store===');
        
        DB::insert(
        'insert into wr_role (
            role_name,
            description,
            mem_id,
            create_date,
            modified_date
        ) values (
            :role_name,
            :description,
            :mem_id,
            now(),
            now()
        )', [
            'role_name'=>$role->get('role_name'),
            'description'=>$role->get('description'),
            'mem_id'=>Auth::user()->id
        ]);
    }

    public function update( $role, $id ) {
        $this->logger->info('===update===');

        DB::update(
        'update wr_role set
            role_name = :role_name,
            description = :description,
            mem_id = :mem_id,
            create_date = now(),
            modified_date = now()
          where role_id = :role_id
        ', [
            'role_id'=>$id,
            'role_name'=>$role->get('role_name'),
            'description'=>$role->get('description'),
            'mem_id'=>Auth::user()->id
        ]);
    }

    public function destroy( $id ) {
        $this->logger->info('===destroy===');

        DB::delete(
        'delete from wr_role
          where role_id = :role_id', ['role_id'=>$id]
        );
    }
}
