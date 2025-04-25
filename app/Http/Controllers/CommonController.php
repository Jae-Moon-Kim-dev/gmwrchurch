<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Common\CommonService;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class CommonController extends Controller
{
    protected $commonService;
    protected $logger;

    public function __construct(CommonService $commonService) {
        $this->commonService = $commonService;

        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel.log'), Logger::INFO));
    }

    public function getMenuType() {
        $this->logger->info('===getMenuType===');
        $menuTypes = $this->commonService->getMenuType();

        if ( !$menuTypes ) {
            return response()->json(['success'=>false, 'message'=>'No Data'], 401);
        } else {
            return response()->json(['success'=>true, 'data'=>$menuTypes], 200);
        }
    }

    public function getCombVisible() {
        $this->logger->info('===getCombVisible===');
        $combVisible =  $this->commonService->getCombVisible();

        if ( !$combVisible ) {
            return response()->json(['success'=>false, 'message'=>'No Data'], 401);
        } else {
            return response()->json(['success'=>true, 'data'=>$combVisible], 200);
        }
    }

    public function getRoles() {
        $this->logger->info('===getRoles===');
        $roles =  $this->commonService->getRoles();

        if ( !$roles ) {
            return response()->json(['success'=>false, 'message'=>'No Data'], 401);
        } else {
            return response()->json(['success'=>true, 'data'=>$roles], 200);
        }
    }
}
