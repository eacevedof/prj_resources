<?php
namespace App\Services;
use \Exception;
use TheFramework\Components\Config\ComponentConfig;

class UploadUrlService extends AppService
{
    private $files;
    private $post;
    private $rootpath;

    private const INVALID_EXTENSIONS = [
        "php","js","py","html","phar","java","sh"
    ];

    private $resources_url;

    public function __construct($post,$files)
    {
        //$this->logd($post,"UploadUrlService.POST");
        //$this->logd($files,"UploadUrlService.FILES");
        $this->post = $post;
        $this->files = $files;
        $this->rootpath = $this->get_env("APP_UPLOADROOT");
        $this->resources_url = $this->get_env("APP_RESOURCES_URL");
    }

    private function mkdir_temp()
    {
        $pathtmp = "$this->rootpath/tmpurl";
        $r = true;
        if(!is_dir($pathtmp)) $r = mkdir($pathtmp);
        if(!$r) throw new Exception("tmpurl dir could not be created");
    }

    private function _slugify($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
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
        $files = $this->post["files"] ?? "";
        if(!$files) throw new Exception("Url files not provided");

        $this->mkdir_temp();
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
            //$filetemp = uniqid()
        }
    }
}

