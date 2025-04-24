<?php

namespace App\Services\Admin;

use App\Repositories\Admin\AdminMemberRepository;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AdminMemberService {
    protected $adminMemberRepository;
    protected $logger;

    public function __construct(AdminMemberRepository $adminMemberRepository) {
        $this->adminMemberRepository = $adminMemberRepository;

        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function getMemberList($member) {

        $this->logger->info('===getMenuList Start===');
        
        return [
            'member_list'=> $this->adminMemberRepository->getMemberList($member),
            'total_cnt'=> $this->adminMemberRepository->getMemberTotalCount()->total_cnt,
        ];
    }
}
