<?php
namespace App\Services;
use \Exception;
use phpDocumentor\Reflection\Types\Self_;

class FilesService extends AppService
{
    private $post;
    private $rootpath;

    private $resources_url;

    private const INVALID_EXTENSIONS = [
        "php","js","py","html","phar","java","sh","htaccess","jar"
    ];

    public function __construct($post)
    {
        //$this->logd($post,"fileservice.post");
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

    private function _add_domain(&$files)
    {
        $urlfolder = $this->resources_url."/";

        if(isset($this->post["folderdomain"]))
            $urlfolder .= "{$this->post["folderdomain"]}/";

        foreach ($files as $i=>$file)
            $files[$i] = $urlfolder.$file;
    }

    private function _get_sorted_desc($pathfolder, $files)
    {
        $dates = [];
        foreach ($files as $file){
            $path = "$pathfolder/$file";
            //$dates[$file] = filectime($path);
            $dates[] = ["file"=>$file, "created-at"=>filectime($path)];
        }
        //krsort($dates);
        $createdat = array_column($dates,"created-at");
        array_multisort($createdat,SORT_DESC, $dates);
        return $dates;
    }

    private function _is_fobidden($pathfile)
    {
        $parts = explode("/",$pathfile);
        $parts = end($parts);
        $parts = explode(".",$parts);
        $ext = end($parts);
        return in_array($ext,self::INVALID_EXTENSIONS);
    }

    private function _remove_forbidden(&$files)
    {
        foreach ($files as $i => $file)
            if($this->_is_fobidden($file))
                unset($files[$i]);
    }

    public function get_files()
    {
        $pathfolder = $this->rootpath;
        $folder = $this->post["folderdomain"] ?? "";
        if($folder) $pathfolder.="/$folder";
        if(!is_dir($pathfolder)) throw new Exception("Folder '$folder' not found");
        $files = $this->_rec_scan($pathfolder);
        $this->_remove_forbidden($files);
        //print_r($files);die;
        $dates = $this->_get_sorted_desc($pathfolder,$files);
        //pr($dates);
        $files = array_column($dates,"file");
        //pr($files);die;
        $this->_add_domain($files);
        return $files;
    }

    private function _get_pathinfo($url)
    {
        $url = trim($url);
        $urlinfo = parse_url($url);
        $path = explode("/",$urlinfo["path"]);
        $filename = end($path);
        $extension = explode(".",$filename);
        $extension = end($extension);

        return [
            "url" => $url,
            "filename" => $filename,
            "extension" => $extension,
            "pathlocal" => str_replace($this->resources_url,$this->rootpath,$url),
        ];
    }

    public function remove()
    {
        //to-do
        $urls = $this->post["urls"] ?? [];
        if(!$urls) throw new Exception("Files not provided");

        $removed = [];
        foreach ($urls as $url)
        {
            if(!$url) continue;
            if(!strstr($url,$this->resources_url)) continue;
            if($this->_is_fobidden($url)) continue;

            $urlinfo = $this->_get_pathinfo($url);
            if(in_array($urlinfo["extension"],self::INVALID_EXTENSIONS))
            {
                $this->add_error("403: $url");
                continue;
            }

            if(!is_file($urlinfo["pathlocal"]))
            {
                $this->add_error("404: $url");
                continue;
            }

            $r = unlink($urlinfo["pathlocal"]);
            if(!$r)
            {
                $this->add_error("501: $url");
                continue;
            }

            $removed[] = $url;
        }

        return $removed;
    }
}
