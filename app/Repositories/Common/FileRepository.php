<?php

namespace App\Repositories\Common;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class FileRepository {
    protected $logger;

    public function __construct() {
        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function getMenuType() {
        $this->logger->info('===getMenuType===');
        
        $menuType = DB::select(
            '
                select meta_type
                    , meta_key
                    , meta_value 
                from wr_meta
                where meta_type = "menu_type";
            '
        );

        return $menuType;
    }

    public function storeFile($file, $uploadDir, $fileName, $category) {
        $this->logger->info('===storeFile===');

        DB::insert(
            '
                insert into wr_file (
                    category,
                    directory, 
                    physical_name, 
                    actual_name, 
                    description, 
                    type, 
                    size, 
                    mem_id, 
                    create_date, 
                    modified_date
                ) values (
                    :category,
                    :directory,
                    :physical_name,
                    :actual_name,
                    :description,
                    :type,
                    :size,
                    :mem_id,
                    now(),
                    now()
                );
            ',
            [
                'category' => $category,
                'directory' => $uploadDir,
                'physical_name' => $fileName,
                'actual_name' => $file->getClientOriginalName(),
                'description' => $file->getClientOriginalName(),
                'type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'mem_id' => Auth::user()->id
            ]
        );
    }
}