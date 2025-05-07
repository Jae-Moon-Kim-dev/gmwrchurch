<?php

namespace App\Services\Auth;

use App\Repositories\Auth\AuthRepository;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use stdClass;

class AuthService {
    protected $authRepository;
    protected $logger;

    public function __construct(AuthRepository $authRepository) {
        $this->authRepository = $authRepository;

        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function getUserById($id) {
        $this->logger->info('===getUserById===');

        return $this->authRepository->getUserById($id);
    }

    public function getMenus() {
        $this->logger->info('===getMenus===');

        return $this->authRepository->getMenus("");
    }

    public function getRecursiveMenu($menus) {
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
                        "url"=>$menu->menu_url,
                        "menuOrder"=>$menu->menu_order,
                        "pathId"=>$menu->path_id,
                        "paths"=>$menu->paths,
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
                    "url"=>$menu->menu_url,
                    "menuOrder"=>$menu->menu_order,
                    "pathId"=>$menu->path_id,
                    "paths"=>$menu->paths,
                    "children"=>getChildren($menu->menu_id, $tempMenus),
                );
                $treeMenus[] = (object)$treeMenu;
            }
        }
        $this->logger->info('===getRecursiveMenu End===');

        return $treeMenus;
    }

    public function getMenu($id) {
        $this->logger->info('===getMenu===');
        return $this->authRepository->getMenus($id);
    }

    public function getRecursiveSideMenu($menu) {
        $this->logger->info('===getRecursiveSideMenu===');
        $tempMenu = collect($menu);

        function getSideChildren ( $parent_id, &$tempMenu ) {
            $children = [];
            foreach($tempMenu as $temp) {
                if ( $temp->parent_menu_id == $parent_id ) {
                    $treeMenu = array(
                        "id"=>$temp->menu_id,
                        "parentId"=>$temp->parent_menu_id,
                        "label"=>$temp->menu_name,
                        "url"=>$temp->menu_url,
                        "menuOrder"=>$temp->menu_order,
                        "pathId"=>$temp->path_id,
                        "paths"=>$temp->paths,
                        "children"=>getSideChildren($temp->menu_id, $tempMenu),
                    );
                    $children[] = (object)$treeMenu;
                }
            }
            return $children;
        }

        foreach($tempMenu as $temp) {
            if ( empty($temp->parent_menu_id) ) {
                $treeMenu = array(
                    "id"=>$temp->menu_id,
                    "parentId"=>$temp->parent_menu_id,
                    "label"=>$temp->menu_name,
                    "url"=>$temp->menu_url,
                    "menuOrder"=>$temp->menu_order,
                    "pathId"=>$temp->path_id,
                    "paths"=>$temp->paths,
                    "children"=>getSideChildren($temp->menu_id, $tempMenu),
                );
                $treeMenu = (object)$treeMenu;
            }
        }
        $this->logger->info('===getRecursiveMenu End===');

        return $treeMenu;
    }
}
