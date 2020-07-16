<?php
namespace App\Services\Security;
use App\Services\AppService;
use \Exception;


class UploadService extends AppService
{
    private $files;
    private $post;
    private $rootpath;

    public function __construct($post,$files)
    {
        $this->post = $post;
        $this->files = $files;

    }

    private function _is_valid()
    {
        if(!$this->post) throw new \Exception("Empty post");
        if(!$this->files) throw new \Exception("Empty files");
    }

    private function _is_postok()
    {


    }

    private function _upload()
    {

    }

    public function get_uploaded()
    {
        $this->_is_valid();

    }

}