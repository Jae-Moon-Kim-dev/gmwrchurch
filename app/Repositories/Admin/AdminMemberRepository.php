<?php

namespace App\Repositories\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AdminMemberRepository {

    protected $logger;

    public function __construct() {
        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function getMemberList ( $member ) {
        $this->logger->info('===getMemberList===');
        $params = [];
        $sql = 'select @rown:= @rown+1 as rownum
              , a.id
              , a.name
              , a.mem_id 
              , a.email 
              , a.cel_num 
              , a.role_id 
              , (select role_name 
                   from wr_role b 
                  where b.role_id = a.role_id) as role_name
              , date_format(a.created_at, "%Y-%m-%d") as created_at
           from users a
          where (@rown:=0) = 0';

          if ( !empty(((object)$member->get('searchQuery'))->role_id) ) {
            $sql = $sql.' and role_id = :role_id';
            $params['role_id'] = ((object)$member->get('searchQuery'))->role_id;
        }
        if ( !empty(((object)$member->get('searchQuery'))->searchParam) 
                && !empty(((object)$member->get('searchQuery'))->searchText) ) {
            $searchParam = ((object)$member->get('searchQuery'))->searchParam;
            $searchText = ((object)$member->get('searchQuery'))->searchText;
            
            if ( $searchParam == 'name' ) {
                $sql .= ' and name = :name';
                $params['name'] = $searchText;
            } else if ( $searchParam == 'mem_id' ) {
                $sql .= ' and mem_id like :mem_id';
                $params['mem_id'] = "%".$searchText."%";
            } else if ( $searchParam == 'email' ) {
                $sql .= ' and email = :email';
                $params['email'] = $searchText;
            } else if ( $searchParam == 'cel_num' ) {
                $sql .= ' and cel_num = :cel_num';
                $params['cel_num'] = $searchText;
            }
        }

        $sql .= ' order by a.id desc limit :page_size offset :page_index';

        $params['page_size'] = ((object)$member->get('pagination'))->pageSize;
        $params['page_index'] = ((object)$member->get('pagination'))->pageSize * ((object)$member->get('pagination'))->pageIndex;

        $members = DB::select($sql, $params);

        return $members;
    }

    public function getMemberTotalCount ($member) {
        $this->logger->info('===getMemberTotalCount===');
        $params = [];
        $sql = 'select count(*) as total_cnt
           from users a
          where 1=1';

        if ( !empty(((object)$member->get('searchQuery'))->role_id) ) {
            $sql = $sql.' and role_id = :role_id';
            $params['role_id'] = ((object)$member->get('searchQuery'))->role_id;
        }
        if ( !empty(((object)$member->get('searchQuery'))->searchParam) 
                && !empty(((object)$member->get('searchQuery'))->searchText) ) {
            $searchParam = ((object)$member->get('searchQuery'))->searchParam;
            $searchText = ((object)$member->get('searchQuery'))->searchText;
            
            if ( $searchParam == 'name' ) {
                $sql .= ' and name = :name';
                $params['name'] = $searchText;
            } else if ( $searchParam == 'mem_id' ) {
                $sql .= ' and mem_id like :mem_id';
                $params['mem_id'] = "%".$searchText."%";
            } else if ( $searchParam == 'email' ) {
                $sql .= ' and email = :email';
                $params['email'] = $searchText;
            } else if ( $searchParam == 'cel_num' ) {
                $sql .= ' and cel_num = :cel_num';
                $params['cel_num'] = $searchText;
            }
        }

        $cnt = collect(DB::select($sql, $params))->first();

        return $cnt;
    }

    public function updateMemberRole ($memberRole) {
        DB::update('
            update users set
                   role_id = :role_id
            where mem_id = :mem_id
        ',[
            'role_id'=> $memberRole->role_id,
            'mem_id'=> $memberRole->mem_id
        ]);
    }

    public function deleteMember ( $member ) {
        DB::delete('
            delete from users
             where mem_id = :mem_id
        ',[
            'mem_id'=> $member->mem_id
        ]);
    }
}
