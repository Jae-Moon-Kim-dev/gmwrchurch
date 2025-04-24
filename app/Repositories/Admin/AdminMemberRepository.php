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
        $members = DB::select(
        'select @rown:= @rown+1 as rownum
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
          where (@rown:=0) = 0
          limit :page_size offset :page_index', [
            'page_size'=> $member->get('pageSize'),
            'page_index'=> ($member->get('pageIndex') * $member->get('pageSize'))
          ]);

        return $members;
    }

    public function getMemberTotalCount () {
        $this->logger->info('===getMemberTotalCount===');
        $cnt = collect(DB::select(
        'select count(*) as total_cnt
           from users a'))->values()->first();

        return $cnt;
    }
}
