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
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function getMenuList() {

        $this->logger->info('===getMenuList Start===');
        $menus = $this->adminMenuRepository->getMenuList();

        $this->logger->info('===getMenuList End===');

        return $this->getRecursiveMenu($menus);
    }

    public function getMenuById($id) {
        $this->logger->info('===getMenuById===');

        return $this->adminMenuRepository->getMenuById($id);
    }

    public function store($menu) {
        $this->logger->info('===store===');
        $this->logger->info('===store==='.print_r($menu, true));
        $this->adminMenuRepository->store($menu);
    }

    public function update($request, $id) {
        $this->logger->info('===update===');

        $this->adminMenuRepository->update($request, $id);
    }

    public function destroy($id) {
        $this->logger->info('===destroy===');

        $this->adminMenuRepository->destroy($id);
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
                    $treeMenu = array(
                        "id"=>$menu->menu_id,
                        "parentId"=>$menu->parent_menu_id,
                        "label"=>$menu->menu_name,
                        "children"=>getChildren($menu->menu_id, $tempMenus),
                    );
                    $children[] = (object)$treeMenu;
                }
            }
            return $children;
        }

        $treeMenus = [];
        foreach ( $tempMenus as $menu ) {
            if ( empty($menu->parent_menu_id) ) {
                $treeMenu = array(
                    "id"=>$menu->menu_id,
                    "parentId"=>$menu->parent_menu_id,
                    "label"=>$menu->menu_name,
                    "children"=>getChildren($menu->menu_id, $tempMenus),
                );
                $treeMenus[] = (object)$treeMenu;
            }
        }
        $this->logger->info('===getRecursiveMenu End===');

        return $treeMenus;

        // $jsonTree = json_encode($treeMenus, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        // $this->logger->info('===getRecursiveMenu==='.$jsonTree);
    }
}
