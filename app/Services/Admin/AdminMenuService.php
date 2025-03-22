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

        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel.log'), Logger::INFO));
    }

    public function getMenuList() {

        $this->logger->info('===getMenuList Start===');
        $menus = $this->adminMenuRepository->getMenuList();

        $this->getRecursiveMenu($menus);

        $this->logger->info('===getMenuList End===');

        return $menus;
    }

    private function getRecursiveMenu($menus) {
        $this->logger->info('===getRecursiveMenu Start===');
        $tempMenus = [];
        foreach ( $menus as $menu ) {
            $menu->children = [];
            $tempMenus[] = $menu;
        }

        function getChildren ( $parent_id, &$tempMenus ) {
            $children = [];
            foreach ( $tempMenus as $menu ) {
                if ( $menu->parent_menu_id == $parent_id ) {
                    $menu->children = getChildren($menu->menu_id, $tempMenus);
                    $children[] = $menu;
                }
            }
            return $children;
        }

        $treeMenus = [];
        foreach ( $tempMenus as $menu ) {
            if ( empty($menu->parent_menu_id) ) {
                $menu->children = getChildren($menu->menu_id, $tempMenus);
                $treeMenus[] = $menu;
            }
        }
        $this->logger->info('===getRecursiveMenu End===');

        // $jsonTree = json_encode($treeMenus, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        // $this->logger->info('===getRecursiveMenu==='.$jsonTree);
    }
}
