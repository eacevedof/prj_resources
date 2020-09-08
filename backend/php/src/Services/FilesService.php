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
                    $result[] =  "$this->resources_url/$filename/$childFilename";
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
        return $files;
    }
    
    public function get_files()
    {
        $folder = $this->post["folder"] ?? "";
        $files = $this->_rec_scan($this->rootpath);
        return $files;
    }

}
