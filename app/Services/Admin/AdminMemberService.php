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
            'total_cnt'=> $this->adminMemberRepository->getMemberTotalCount($member)->total_cnt,
        ];
    }

    public function updateMemberRole($request) {
        $this->logger->info('===updateMemberRole===');
        $memberRoles = $request->get("roleMembers");

        foreach ( $memberRoles as $memberRole ) {
            $this->logger->info('===updateMemberRole===');

            $this->adminMemberRepository->updateMemberRole((object)$memberRole);
        }
    }

    public function deleteMember($request) {
        $this->logger->info('===deleteMember===');
        $members = $request->get("deleteMembers");

        foreach ( $members as $member ) {
            $this->logger->info('===deleteMember===');

            $this->adminMemberRepository->deleteMember((object)$member);
        }
    }
}
