<?php

namespace App\Services\Common;

use App\Repositories\Common\BoardRepository;
use App\Repositories\Common\FileRepository;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Facades\Storage;

class BoardService {
    protected $boardRepository;
    protected $fileRepository;
    protected $logger;

    public function __construct(BoardRepository $boardRepository, FileRepository $fileRepository) {
        $this->boardRepository = $boardRepository;
        $this->fileRepository = $fileRepository;

        $this->logger = new Logger(__CLASS__);
        $this->logger->pushHandler(new StreamHandler(storage_path('logs/laravel_'. date("Y-m-d") .'.log')));
    }

    public function savePage($request) {
        $this->logger->info('===savePage===');

        $page = $this->boardRepository->getPage($request->input("menuId"));

        if ( count($page) > 0 ) {
            $this->boardRepository->updatePage((collect($page)->first())->board_id, $request->input("content"));
        } else {
            $this->boardRepository->insertPage($request->input("menuId"), $request->input("content"));
        }
    }

    public function getPage($menuId) {
        $this->logger->info('===getPage===');

        $page = $this->boardRepository->getPage($menuId);

        return collect($page)->first();
    }

    public function getRoleByUser($request) {
        $this->logger->info('===getRoleByUser===');

        $role = $this->boardRepository->getRoleByUser($request->input('menuId'), $request->input('roleId'));

        return collect($role)->first();
    }

    public function getBoard($request) {
        $this->logger->info('===getBoard===');

        return [
            'board_list'=> $this->boardRepository->getBoard($request),
            'total_cnt'=> $this->boardRepository->getBoardTotalCount($request)->total_cnt,
        ];
    }

    public function getBoardById($boardId) {
        $this->logger->info('===getBoardById===');

        $board = $this->boardRepository->getBoardById($boardId);

        return collect($board)->first();
    }

    public function insertBoard($request) {
        $this->logger->info('===insertBoard===');

        $files = $request->file('files');
        $uploadDir = 'bbs/'.$request->input('menu_id');

        if ( $request->hasFile('files') )
        {
            $this->logger->info("===upload===".json_encode($request->input('menu_id')));

            Storage::disk('public')->makeDirectory($uploadDir);
            $idx = 0;
            $isArrayFile = false;

            $isArrayFile = is_array($files);
            $this->logger->info("===upload===isArrayFile: ".$isArrayFile);

            if ( $isArrayFile ) {
                foreach ( $files as $file )
                {
                    $this->logger->info("===upload===".json_encode($file->getClientMimeType()));
                    $this->logger->info("===upload===".json_encode($file->getSize()));

                    $fileName = $this->RandomName($idx);
                    Storage::disk('public')->putFileAs($uploadDir, $file, $fileName);
                    // $file->storeAs('public/'.$uploadDir, $fileName);
                    $boardId = $this->boardRepository->insertBoard($request);
                    $this->logger->info("===upload===boardId: ".$boardId);
                    $this->fileRepository->storeFile($file, $uploadDir, $fileName, $request->input('menu_id'), $boardId);
                    $idx++;
                    // return Storage::url($uploadDir.'/'.$fileName);
                }
            } else {
                $this->logger->info("===upload===".json_encode($files->getClientMimeType()));
                $this->logger->info("===upload===".json_encode($files->getSize()));

                $fileName = $this->RandomName($idx);
                Storage::disk('public')->putFileAs($uploadDir, $files, $fileName);
                // $file->storeAs('public/'.$uploadDir, $fileName);
                $boardId = $this->boardRepository->insertBoard($request);
                $this->logger->info("===upload===boardId: ".$boardId);
                $this->fileRepository->storeFile($files, $uploadDir, $fileName, $request->input('menu_id'), $boardId);
                // return Storage::url($uploadDir.'/'.$fileName);
            }

        }
    }

    private function RandomName ($idx) {
        $fileName = '';

        $fileName = time().$idx;

        $this->logger->info('RandomName____'.$fileName);

        return $fileName;
    }

}
