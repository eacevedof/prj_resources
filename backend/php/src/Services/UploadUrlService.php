<?php
namespace App\Services;
use \Exception;
use TheFramework\Components\Config\ComponentConfig;

class UploadUrlService extends AppService
{
    private $files = [];
    private $post;
    private $rootpath;

    private const INVALID_EXTENSIONS = [
        "php","js","py","html","phar","java","sh"
    ];

    private $resources_url;

    public function __construct($post)
    {
        //$this->logd($post,"UploadUrlService.POST");
        //$this->logd($files,"UploadUrlService.FILES");
        $this->post = $post;
        $this->rootpath = $this->get_env("APP_UPLOADROOT");
        $this->resources_url = $this->get_env("APP_RESOURCES_URL");
    }

    private function _get_mkdir()
    {
        $public = "{$this->post["folderdomain"]}/".date("Ymd");
        $pathdate = "$this->rootpath/$public";
        $r = true;
        if(!is_dir($pathdate)) $r = mkdir($pathdate);
        if(!$r) throw new Exception("Folder date dir could not created");
        return [
            "public" => $public,
            "pathdate" => $pathdate,
        ];
    }

    private function _slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace("~[^\pL\d]+~u", "-", $text);
        // transliterate
        $text = iconv("utf-8", "us-ascii//TRANSLIT", $text);
        // remove unwanted characters
        $text = preg_replace("~[^-\w]+~", "", $text);
        // trim
        $text = trim($text, "-");
        // remove duplicate -
        $text = preg_replace("~-+~", "-", $text);
        // lowercase
        $text = strtolower($text);
        if (empty($text)) {
            return "n-a";
        }

        return $text;
    }

    private function _get_file($url)
    {
        $urlinfo = parse_url($url);
        $urlfile = $urlinfo["path"];
        $parts = explode("/",$urlfile);
        $file = end($parts);
        return $file;
    }

    private function _get_slug($url)
    {
        $file = $this->_get_file($url);
        $file = strtolower($file);
        $slug = $this->_slugify($file);
        return $slug;
    }

    public function get_uploaded()
    {
        if(!($this->post["folderdomain"] ?? "")) throw new Exception("No folder domain provided");

        $files = $this->post["files"] ?? "";
        if(!$files) throw new Exception("Url files not provided");
        
        
        //$this->_mkdir();
        if(is_string($files)){
            $files = explode(";",$files);
        }

        foreach ($files as $urlfile)
        {
            $urlfile = trim($urlfile);
            if(strpos($urlfile,"http")!==0) continue;
            //if() extension
            $content = file_get_contents($urlfile);
            $slug = $this->_get_slug($urlfile);
            $pathdir = $this->_get_mkdir();
            $pathsave = $pathdir["pathdate"]."/".$slug;
            $r = file_put_contents($pathsave, $content);
            if(!$r)
                $this->add_error($urlfile);
            else
                $this->files[] = $this->resources_url."/".$pathdir["public"];
        }

        return $this->files;
    }
}

