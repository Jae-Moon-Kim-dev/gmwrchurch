<?php

namespace App\Repositories\Common;

use Illuminate\Support\Facades\DB;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class CommonRepository {
    protected $logger;

    public function __construct() {
        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel.log'), Logger::INFO));
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

    public function getCombVisible() {
        $this->logger->info('===getCombVisible===');
        
        $menuType = DB::select(
            '
                select meta_type
                    , meta_key
                    , meta_value 
                from wr_meta
                where meta_type = "visible_yn";
            '
        );

        return $menuType;
    }
}