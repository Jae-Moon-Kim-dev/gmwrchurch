<?php

namespace App\Services\Common;

use App\Repositories\Common\FileRepository;
use Illuminate\Foundation\Http\FormRequest;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class FileService {
    protected $fileRepository;
    protected $logger;

    public function __construct(FileRepository $fileRepository) {
        $this->fileRepository = $fileRepository;

        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function upload(FormRequest $request) {
        $this->logger->info('===upload===');

        $this->logger->info("===upload===".json_encode($request->file->getClientMimeType()));
        $this->logger->info("===upload===".json_encode($request->file->getSize()));
        $this->logger->info("===upload===".json_encode($request->input('type')));

        // $menuTypeCodes = $this->fileRepository->getMenuType();


        
        // return $menuTypes;
    }
}