<?php
namespace App\Services;
use \Exception;
use TheFramework\Components\Config\ComponentConfig;

class FilesService extends AppService
{
    private $post;
    private $rootpath;

    private $resources_url;

    public function __construct($post)
    {
        $this->post = $post;
        $this->rootpath = $this->get_env("APP_UPLOADROOT");
        $this->resources_url = $this->get_env("APP_RESOURCES_URL");
    }
    
    private function _rec_scan($dir)
    {
        $result = [];
        foreach(scandir($dir) as $filename) 
        {
            if($filename[0] === ".") continue;
            $filePath = $dir . "/" . $filename;

            if (is_dir($filePath)) {
                foreach ($this->_rec_scan($filePath) as $childFilename) {
                    $result[] =  "$filename/$childFilename";
                }
            }
            else {
                $result[] = $filename;
            }
        }
        return $result;
    }
    
    public function get_folders()
    {
        $files = scandir($this->rootpath);
        foreach ($files as $i => $file)
            if(!is_dir($this->rootpath."/".$file) || in_array($file,[".",".."]))
                unset($files[$i]);
        return array_values($files);
    }
    
    public function get_files()
    {
        $folder = $this->post["folder"] ?? "";
        //pr(scandir($this->rootpath));die;
        $files = $this->_rec_scan($this->rootpath);
        return $files;
    }

}
