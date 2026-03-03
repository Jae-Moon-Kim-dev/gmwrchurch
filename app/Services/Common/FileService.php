<?php

namespace App\Services\Common;

use App\Repositories\Common\FileRepository;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Foundation\Http\FormRequest;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
        $files = $request->file('file');
        $uploadDir = 'bbs/'.$request->input('type');

        $this->logger->info("===upload===".json_encode($request->file('file')->getClientMimeType()));
        $this->logger->info("===upload===".json_encode($request->file('file')->getSize()));
        $this->logger->info("===upload===".json_encode($request->input('type')));

        if ( $request->hasFile('file') )
        {
            Storage::disk('public')->makeDirectory($uploadDir);
            $idx = 0;
            $isArrayFile = false;

            $isArrayFile = is_array($files);

            if ( $isArrayFile ) {
                foreach ( $files as $file )
                {
                    $fileName = $this->RandomName($idx);
                    Storage::disk('public')->putFileAs($uploadDir, $file, $fileName);
                    // $file->storeAs('public/'.$uploadDir, $fileName);
                    $this->fileRepository->storeFile($file, $uploadDir, $fileName, $request->input('type'), null);
                    $idx++;
                    return Storage::url($uploadDir.'/'.$fileName);
                }
            } else {
                $fileName = $this->RandomName($idx);
                Storage::disk('public')->putFileAs($uploadDir, $files, $fileName);
                // $file->storeAs('public/'.$uploadDir, $fileName);
                $this->fileRepository->storeFile($files, $uploadDir, $fileName, $request->input('type'), null);
                return Storage::url($uploadDir.'/'.$fileName);
            }

        }

    }

    private function RandomName ($idx) {
        $fileName = '';

        $fileName = time().$idx;

        $this->logger->info('RandomName____'.$fileName);

        return $fileName;
    }
}
