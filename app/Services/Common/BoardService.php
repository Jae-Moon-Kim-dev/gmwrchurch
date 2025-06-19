<?php

namespace App\Services\Common;

use App\Repositories\Common\BoardRepository;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class BoardService {
    protected $boardRepository;
    protected $logger;

    public function __construct(BoardRepository $boardRepository) {
        $this->boardRepository = $boardRepository;

        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function savePage($request) {
        $this->logger->info('===savePage===');

        $page = $this->boardRepository->getPage($request->input("menuId"));

        if ( count($page) > 0 ) {
            $this->boardRepository->updatePage((collect($page)->first())->board_id, $request->input("content"));
        } else {
            $this->boardRepository->insertPage($request->input("menuId"), $request->input("content"));
        }
    }

    public function getPage($menuId) {
        $this->logger->info('===getPage===');

        $page = $this->boardRepository->getPage($menuId);

        return collect($page)->first();
    }

}
