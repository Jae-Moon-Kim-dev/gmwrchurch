<?php

namespace App\Http\Controllers;

use App\Services\Common\BoardService;
use Illuminate\Foundation\Http\FormRequest;
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

    public function getRoleByUser(Request $request) {
        $this->logger->info('===getRoleByUser===');

        $role =  $this->boardService->getRoleByUser($request);

        if ( !$role ) {
            return response()->json(['success'=>false, 'message'=>'No Data'], 401);
        } else {
            return response()->json(['success'=>true, 'data'=>$role], 200);
        }
    }

    public function getBoard(Request $request) {
        $this->logger->info('===getBoard===');

        $board =  $this->boardService->getBoard($request);

        if ( !$board ) {
            return response()->json(['success'=>false, 'message'=>'No Data'], 401);
        } else {
            return response()->json(['success'=>true, 'data'=>$board], 200);
        }
    }

    public function getBoardById(string $boardId) {
        $this->logger->info('===getBoardById===');
        $board =  $this->boardService->getBoardById($boardId);

        if ( !$board ) {
            return response()->json(['success'=>false, 'message'=>'No Data'], 401);
        } else {
            return response()->json(['success'=>true, 'data'=>$board], 200);
        }
    }

    public function insertBoard(FormRequest $request) {
        $this->logger->info('===insertBoard===');
        $this->boardService->insertBoard($request);

        return response()->json(['success'=>true, 'data'=>true], 200);
    }
}
