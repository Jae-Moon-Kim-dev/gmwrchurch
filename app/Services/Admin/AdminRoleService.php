<?php

namespace App\Services\Admin;

use App\Repositories\Admin\AdminRoleRepository;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AdminRoleService {
    protected $adminRoleRepository;
    protected $logger;

    public function __construct(AdminRoleRepository $adminRoleRepository) {
        $this->adminRoleRepository = $adminRoleRepository;

        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function getRoleList() {

        $this->logger->info('===getMenuList Start===');
        
        return $this->adminRoleRepository->getRoleList();
    }

    public function store($role) {
        $this->logger->info('===store Start===');

        $this->adminRoleRepository->store($role);
    }

    public function update($role, $id) {
        $this->adminRoleRepository->update($role, $id);
    }

    public function destroy($id) {
        $this->logger->info('===destroy Start===');

        $this->adminRoleRepository->destroy($id);
    }
}
