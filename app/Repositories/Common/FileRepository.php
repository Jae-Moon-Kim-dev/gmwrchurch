<?php

namespace App\Repositories\Common;

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
}