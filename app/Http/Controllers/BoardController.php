<?php

namespace App\Http\Controllers;

use App\Services\Common\BoardService;
use Illuminate\Http\Request;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class BoardController extends Controller
{
    protected $boardService;
    protected $logger;

    public function __construct(BoardService $boardService) {
        $this->boardService = $boardService;

        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function savePage(Request $request) {
        $this->logger->info('===savePage===');
        $this->boardService->savePage($request);

        return response()->json(['success'=>true, 'data'=>true], 200);
    }

    public function getPage(string $menuId) {
        $this->logger->info('===getPage===');

        $page =  $this->boardService->getPage($menuId);

        if ( !$page ) {
            return response()->json(['success'=>false, 'message'=>'No Data'], 401);
        } else {
            return response()->json(['success'=>true, 'data'=>$page], 200);
        }
    }

}
