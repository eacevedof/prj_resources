<?php
namespace App\Services;
use \Exception;

class UploadService extends AppService
{
    private $files;
    private $post;
    private $rootpath;
    private $url;

    public function __construct($post,$files)
    {
        $this->post = $post;
        $this->files = $files;
        $this->rootpath = $this->get_env("APP_UPLOADROOT");
    }

    private function _is_valid()
    {
        if(!$this->post) throw new Exception("Empty post");
        if(!$this->files) throw new Exception("Empty files");
        if(!isset($this->post["folderdomain"])) throw new Exception("No domain selected");
    }

    private function _is_postok()
    {

    }

    private function _get_basename($rawname)
    {
        return basename($rawname);
    }

    private function _get_saved($pathfinal)
    {
        return move_uploaded_file($this->files["fil-one"]["tmp_name"],$pathfinal);
    }
    
    private function _upload()
    {
        $today = date("Ymd");
        $pathdest = "{$this->rootpath}/{$today}/";
        if(!file_exists($pathdest)) mkdir($pathdest, 0777, true);
        $filename = $this->_get_basename($this->files["fil-one"]["name"]);
        $pathfinal = "{$pathdest}/{$filename}";
        if(is_file($pathfinal)) unlink($pathfinal);
        //print_r($pathfinal);die;
        $r = $this->_get_saved($pathfinal);
        if(!$r)
            throw new Exception("Error uploading file");
    }

    public function get_uploaded()
    {
        $this->_is_valid();
        $this->_upload();
        return $this->url;
    }

}