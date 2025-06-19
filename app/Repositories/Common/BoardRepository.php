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
}
