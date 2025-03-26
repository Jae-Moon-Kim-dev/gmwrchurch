<?php

namespace App\Services\Common;

use App\Repositories\Common\CommonRepository;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class CommonService {
    protected $commonRepository;
    protected $logger;

    public function __construct(CommonRepository $commonRepository) {
        $this->commonRepository = $commonRepository;

        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel.log'), Logger::INFO));
    }

    public function getMenuType() {
        $this->logger->info('===getMenuType===');

        $menuTypes = [];
        $menuTypeCodes = $this->commonRepository->getMenuType();

        foreach( $menuTypeCodes as $menuTypeCode ) {
            $menuType = array(
                "value"=> $menuTypeCode->meta_key,
                "label"=> $menuTypeCode->meta_value
            );
            $menuTypes[] = (object)$menuType;
        };

        return $menuTypes;
    }

    public function getCombVisible() {
        $this->logger->info('===getCombVisible===');

        $commons = [];
        $commonCodes = $this->commonRepository->getCombVisible();

        foreach( $commonCodes as $commonCode ) {
            $common = array(
                "value"=> $commonCode->meta_key,
                "label"=> $commonCode->meta_value
            );
            $commons[] = (object)$common;
        };

        return $commons;
    }
    
}