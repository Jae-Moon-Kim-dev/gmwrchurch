<?php

namespace App\Repositories\Common;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class BoardRepository {
    protected $logger;

    public function __construct() {
        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function getRoleByUser($menuId, $roleId) {
        $this->logger->info('===getRoleByUser===');

        $role = DB::select(
            '
                select menu_id
                     , role_id
                     , read_yn
                     , write_yn
                     , admin_yn
                  from wr_menu_role
                 where menu_id = :menu_id
                   and role_id = :role_id
            ', [
                'menu_id' => $menuId,
                'role_id' => $roleId
            ]
        );

        return $role;
    }

    public function getBoardById($boardId) {
        $this->logger->info('===getBoardById===');

        $page = DB::select(
            '
                select a.board_id
                     , a.board_type
                     , a.board_title
                     , a.board_content
                     , a.menu_id
                     , a.like_count
                     , a.views_count
                     , (select x.name
                          from users x
                         where x.id = a.board_user_id) as board_user_name
                     , date_format(a.create_date, "%Y-%m-%d") board_user_date
                     , a.admin_yn
                     , a.noti_yn
                  from wr_board a
                 where a.board_id = :board_id
            ', [
                'board_id' => $boardId
            ]
        );

        return $page;
    }

    public function insertPage($menuId, $content) {
        $this->logger->info('===insertPage===');

        DB::insert(
            '
                insert into wr_board (
                    menu_id,
                    board_type,
                    board_content,
                    board_user_id,
                    create_date,
                    modified_date
                ) values (
                    :menu_id,
                    :board_type,
                    :board_content,
                    :board_user_id,
                    now(),
                    now()
                )
            ',
            [
                'menu_id' => $menuId,
                'board_type' => 'page',
                'board_content' => $content,
                'board_user_id' => Auth::user()->id
            ]
        );
    }

    public function updatePage($boardId, $content) {
        $this->logger->info('===updatePage===');

        DB::update(
            '
                update wr_board set
                    board_content = :board_content,
                    board_user_id = :board_user_id,
                    modified_date = now()
                where board_id = :board_id
            ',
            [
                'board_id' => $boardId,
                'board_content' => $content,
                'board_user_id' => Auth::user()->id
            ]
        );
    }

    public function getBoard($request) {
        $this->logger->info('===getBoard===');
        $params = [];
        $sql = 'select @rown:= @rown+1 as rownum
              , a.board_id
              , a.menu_id
              , a.board_type
              , a.board_title
              , a.board_user_id
              , (select x.name
                   from users x
                  where x.id = a.board_user_id) as created_name
              , date_format(a.create_date, "%Y-%m-%d") create_date
              , a.views_count
              , a.like_count
           from wr_board a
          where a.menu_id = :menu_id
            and (@rown:=0) = 0';

        $params['menu_id'] = $request->input('menuId');
        $this->logger->info('===getBoard=== Line 128');
        $this->logger->info('===getBoard===pageSize : '.((object)$request->get('pagination'))->pageSize);
        $this->logger->info('===getBoard===pageIndex : '.((object)$request->get('pagination'))->pageIndex);
        $this->logger->info('===getBoard=== Line 131');
        $params['page_size'] = ((object)$request->get('pagination'))->pageSize;
        $params['page_index'] = ((object)$request->get('pagination'))->pageSize * ((object)$request->get('pagination'))->pageIndex;
        $this->logger->info('===getBoard=== Line 134');
        $sql .= ' order by a.board_id desc limit :page_size offset :page_index';

        $board = DB::select($sql, $params);

        return $board;
    }

    public function getBoardTotalCount($request) {
        $this->logger->info('===getBoard===');

        $cnt = collect(DB::select(
            '
                select count(*) as total_cnt
                  from wr_board a
                 where 1=1
                   and a.menu_id = :menu_id
            ', [
                'menu_id' => $request->input('menuId')
            ]
        ))->first();

        return $cnt;
    }

    public function getPage($menuId) {
        $this->logger->info('===getPage===');

        $page = DB::select(
            '
                select board_id
                     , menu_id
                     , board_type
                     , board_content
                  from wr_board
                 where menu_id = :menu_id
            ', [
                'menu_id' => $menuId
            ]
        );

        return $page;
    }

    public function insertBoard($board) {
        $this->logger->info('===insertBoard===');

        $boardId = DB::table('wr_board')->insertGetId([
            'menu_id' => $board->input('menu_id'),
            'board_type' => $board->input('board_type'),
            'board_title' => $board->input('board_title'),
            'board_content' => $board->input('board_content'),
            'noti_yn' => $board->input('noti_yn'),
            'admin_yn' => $board->input('admin_yn'),
            'board_user_id' => Auth::user()->id,
            'create_date' => now(),
            'modified_date' => now(),
        ]);

        return $boardId;
    }
}
