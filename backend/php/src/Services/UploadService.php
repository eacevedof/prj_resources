<?php
namespace App\Services;
use \Exception;

class UploadService extends AppService
{
    private $files;
    private $post;
    private $rootpath;
    private $url;

    const INVALID_EXTENSIONS = [
        "php","js","py",
    ];

    public function __construct($post,$files)
    {
        $this->post = $post;
        $this->files = $files;
        $this->rootpath = $this->get_env("APP_UPLOADROOT");
    }

    private function _is_valid()
    {
        if(!$this->rootpath) throw new Exception("missing env UPLOADROOT");
        if(!$this->post) throw new Exception("Empty post");
        if(!$this->files) throw new Exception("Empty files");
        if(!isset($this->post["folderdomain"]) || trim($this->post["folderdomain"])==="") throw new Exception("No domain selected");
    }

    private function _get_basename($rawname){return basename($rawname);}

    private function _get_extension($pathfile){return pathinfo($pathfile, PATHINFO_EXTENSION);}

    private function _get_saved($pathfinal)
    {
        return move_uploaded_file($this->files["fil-one"]["tmp_name"],$pathfinal);
    }

    private function _is_invalid($extension)
    {
        $extension = trim($extension);
        $extension = strtolower($extension);
        return in_array($extension,self::INVALID_EXTENSIONS);
    }

    private function _upload()
    {
        $extension = $this->_get_extension($this->files["fil-one"]["name"]);
        if($this->_is_invalid($extension)){
            $this->add_error("file: {$this->files["fil-one"]["name"]} contains forbidden extension");
            return;
        }

        $today = date("Ymd");
        $folderdomain = $this->post["folderdomain"];
        $pathdest = "{$this->rootpath}/$folderdomain/{$today}";
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
/*
'fil-one' =>
array (
'name' => 'trello-397002984.jpg',
'type' => 'image/jpeg',
'tmp_name' => '/private/var/folders/yt/g9dtf4cj40s6m5b4m_rzjz8m0000gn/T/phpKQHL21',
'error' => 0,
'size' => 504284,
),*
*/