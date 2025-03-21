<?php

namespace App\Services\Admin;

use App\Repositories\Admin\AdminMenuRepository;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AdminMenuService {
    protected $adminMenuRepository;
    protected $logger;

    public function __construct(AdminMenuRepository $adminMenuRepository) {
        $this->adminMenuRepository = $adminMenuRepository;

        $this->logger = new Logger('AdminMenuService');
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel.log'), Logger::INFO));
    }

    public function getMenuList() {

        $menus = $this->adminMenuRepository->getMenuList();

        $this->logger->info('===로그찍기===');

        return $menus;
    }
}
