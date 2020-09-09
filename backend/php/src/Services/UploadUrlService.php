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
        $ext = explode(".",$file);
        $ext = end($ext);
        $filenoext = str_replace($ext,"",$file);
        $slug = $this->_slugify($filenoext);
        return [
            "filenoext"     => $filenoext,
            "extension"     => $ext,
            "slug"          => $slug,
        ];
    }

    private function _get_suggest_name($urlfile)
    {
        if(!strstr($urlfile,"=")) return "";
        $parts = explode("=",$urlfile);
        $name = trim($parts[1] ?? "");
        if(!$name) return "";
        $name = $this->_slugify($name);
        return $name;
    }

    private function _get_real_url($urlfile)
    {
        if(!strstr($urlfile,"=")) return trim($urlfile);
        $parts = explode("=",$urlfile);
        $url = trim($parts[0] ?? "");
        return $url;
    }

    public function get_uploaded()
    {
        if(!($this->post["folderdomain"] ?? "")) throw new Exception("No folder domain provided");

        $files = $this->post["files"] ?? "";
        if(!$files) throw new Exception("Url files not provided");
        
        //pr($files);die("files:)");
        //$this->_mkdir();
        if(is_string($files)){
            $files = explode(";",$files);
        }

        foreach ($files as $rawurl)
        {
            $urlfile = $this->_get_real_url($rawurl);
            if(strpos($urlfile,"http")!==0) continue;
            //if() extension

            $slug = $this->_get_slug($urlfile);

            if(in_array($slug["extension"],self::INVALID_EXTENSIONS)){
                $this->add_error($urlfile);
                continue;
            }

            $pathdir = $this->_get_mkdir();
            $content = file_get_contents($urlfile);

            $savename = $slug["slug"];
            $suggestname = $this->_get_suggest_name($rawurl);
            //$this->logd($urlfile,"urlfile");
            //$this->logd($suggestname,"suggetsname");
            if($suggestname) $savename = $suggestname;
            $filesave = "{$savename}.{$slug["extension"]}";
            $pathsave = "{$pathdir["pathdate"]}/$filesave";
            $r = file_put_contents($pathsave, $content);
            if(!$r)
                $this->add_error($urlfile);
            else
                $this->files[] = $this->resources_url."/".$pathdir["public"]."/{$filesave}";
        }

        return $this->files;
    }
}

