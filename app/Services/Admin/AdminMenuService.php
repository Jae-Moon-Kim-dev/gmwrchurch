<?php

namespace App\Services\Admin;

use App\Repositories\Admin\AdminMenuRepository;

class AdminMenuService {
    protected $adminMenuRepository;

    public function __construct(AdminMenuRepository $adminMenuRepository) {
        $this->adminMenuRepository = $adminMenuRepository;
    }

    public function getMenuList() {
        return $this->adminMenuRepository->getMenuList();
    }
}
