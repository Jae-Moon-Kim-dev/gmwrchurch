<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminMemberService;
use Illuminate\Http\Request;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class AdminMemberController extends Controller
{
    protected $adminMemberService;
    protected $logger;

    public function __construct(AdminMemberService $adminMemberService) {
        $this->adminMemberService = $adminMemberService;

        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function index (Request $request) {
        $this->logger->info('===index===');
        $members = $this->adminMemberService->getMemberList($request);

        if ( !$members ) {
            return response()->json(['success'=>false, 'message'=>'No Data'], 401);
        } else {
            return response()->json(['success'=>true, 'data'=>$members], 200);
        }
    }
}
