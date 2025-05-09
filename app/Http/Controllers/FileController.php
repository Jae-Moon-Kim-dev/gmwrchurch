<?php

namespace App\Http\Controllers;

use App\Services\Common\FileService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use stdClass;

class FileController extends Controller
{
    protected $fileService;
    protected $logger;

    public function __construct(FileService $fileService) {
        $this->fileService = $fileService;
        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }
     
    public function upload (FormRequest $request) {
        $this->logger->info("===upload===");
        $this->fileService->upload($request);
        $file = new stdClass();

        if ( !$file ) {
            return response()->json(['success'=>false, 'message'=>'No Data'], 401);
        } else {
            return response()->json(['success'=>true, 'data'=>$file], 200);
        }
    }
}
