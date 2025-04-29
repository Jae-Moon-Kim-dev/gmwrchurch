<?php

namespace App\Services\Admin;

use App\Repositories\Admin\AdminPermissionRepository;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use function PHPUnit\Framework\isEmpty;

class AdminPermissionService {
    protected $adminPermissionRepository;
    protected $logger;

    public function __construct(AdminPermissionRepository $adminPermissionRepository) {
        $this->adminPermissionRepository = $adminPermissionRepository;

        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function getMenuRoleList() {

        $this->logger->info('===getMenuList Start===');
        $menuRoles = $this->adminPermissionRepository->getMenuRoleList();

        $this->logger->info('===getMenuList End===');

        return $this->getMenuRoles($menuRoles);
    }

    private function getMenuRoles($menuRoles) {
        $menuRoleList = [];
        foreach ( $menuRoles as $menuRole ) {
            $roleYn = ((!isEmpty($menuRole->read_yn) && $menuRole->read_yn == 'Y')
                        && (!isEmpty($menuRole->write_yn) && $menuRole->write_yn == 'Y')
                        && (!isEmpty($menuRole->admin_yn) && $menuRole->admin_yn == 'Y')) ? 'N' : 'Y';
            if ( $menuRole->role_id == 3 ) {
                $menuRole = array(
                    'parent_menu_id'=> $menuRole->parent_menu_id,
                    'parent_menu_name'=> $menuRole->parent_menu_name,
                    'menu_id'=> $menuRole->menu_id,
                    'menu_name'=> $menuRole->menu_name,
                    'role_id'=> $menuRole->role_id,
                    'role_name'=> $menuRole->role_name,
                    'role_yn'=> $roleYn,
                    'read_yn'=> $menuRole->read_yn,
                    'write_yn'=> $menuRole->write_yn,
                    'admin_yn'=> $menuRole->admin_yn,
                    'write_disable_yn'=> 'Y',
                    'admin_disable_yn'=> 'Y'
                );
            } else {
                $menuRole = array(
                    'parent_menu_id'=> $menuRole->parent_menu_id,
                    'parent_menu_name'=> $menuRole->parent_menu_name,
                    'menu_id'=> $menuRole->menu_id,
                    'menu_name'=> $menuRole->menu_name,
                    'role_id'=> $menuRole->role_id,
                    'role_name'=> $menuRole->role_name,
                    'role_yn'=> $roleYn,
                    'read_yn'=> $menuRole->read_yn,
                    'write_yn'=> $menuRole->write_yn,
                    'admin_yn'=> $menuRole->admin_yn,
                    'write_disable_yn'=> 'N',
                    'admin_disable_yn'=> 'N'
                );
            }
            $menuRoleList[] = (object)$menuRole;
        }

        return $menuRoleList;
    }
}
