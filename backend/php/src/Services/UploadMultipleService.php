<?php
namespace App\Services;
use \Exception;
use TheFramework\Components\Config\ComponentConfig;

class UploadMultipleService extends AppService
{
    private $files;
    private $post;
    private $rootpath;
    private $urls = [];
    private $positions;

    private const INVALID_EXTENSIONS = [
        "php","js","py","html","phar","java","sh","htaccess","jar"
    ];

    private $resources_url;

    public function __construct($post,$files)
    {
        $this->post = $post;
        $this->files = $files["files"] ?? [];
        $this->positions = $this->_get_positions();

        $this->rootpath = $this->get_env("APP_UPLOADROOT");
        $this->resources_url = $this->get_env("APP_RESOURCES_URL");
    }

    private function _get_domains()
    {
        $sPathfile = $_ENV["APP_DOMAINS"] ?? __DIR__.DIRECTORY_SEPARATOR."domains.prod.json";
        //print($sPathfile);die;
        $arconf = (new ComponentConfig($sPathfile))->get_content();
        return $arconf;
    }

    private function _exceptions()
    {
        if(!trim($this->rootpath)) throw new Exception("missing env UPLOADROOT");
        if(!$this->post) throw new Exception("Empty post");
        if(!$this->files) throw new Exception("Empty files");

        if(!isset($this->post["folderdomain"]) || trim($this->post["folderdomain"])==="") throw new Exception("No domain selected");
        if(!in_array(trim($this->post["folderdomain"]),$this->_get_domains())) throw new Exception("Forbidden folderdomain: {$this->post["folderdomain"]}");
    }
  
    private function _get_basename($rawname){return basename($rawname);}

    private function _get_extension($pathfile){return pathinfo($pathfile, PATHINFO_EXTENSION);}

    private function _get_saved($pathfinal,$arinfo){

        $r = move_uploaded_file($arinfo["tmp_name"],$pathfinal);
        if(!$r) {
            $error = "Error moving: {$arinfo["tmp_name"]} to $pathfinal";
            $this->logd($error,"uploadmultiple._get_saved");
            $this->add_error($error);
        }
        return $r;
    }

    private function _is_invalid($extension)
    {
        $extension = trim($extension);
        $extension = strtolower($extension);
        return in_array($extension,self::INVALID_EXTENSIONS);
    }

    private function _is_oversized($size){return $size > $this->_get_maxsize();}

    private function _get_maxsize(){return UploadService::get_maxsize_bytes();}

    private function _get_fileinfo($ipos)
    {
        return [
            "name"      => $this->files["name"][$ipos],
            "type"      => $this->files["type"][$ipos],
            "tmp_name"  => $this->files["tmp_name"][$ipos],
            "error"     => $this->files["error"][$ipos],
            "size"      => (int) $this->files["size"][$ipos],
        ];
    }

    private function _get_cleaned($filename)
    {
        $cleaned = strtolower($filename);
        $cleaned = str_replace(" ","-",$cleaned);
        return $cleaned;
    }

    private function _is_validinfo($arinfo)
    {
        if($arinfo["error"]){
            $error = "file: {$arinfo["name"]} not uploaded. It contains forbidden extension";
            $this->add_error($error);
            return false;
        }

        $extension = $this->_get_extension($arinfo["name"]);
        if($this->_is_invalid($extension)){
            $error = "file: {$arinfo["name"]} not uploaded. It contains forbidden extension";
            $this->add_error($error);
            return false;
        }

        if($arinfo["size"] === 0 ){
            $maxsize = $this->_get_maxsize();
            $error = "filesize is: 0. May be it is bigger than allowed ($maxsize bytes)";
            $this->add_error($error);
            return false;
        }

        if($this->_is_oversized($arinfo["size"])){
            $maxsize = $this->_get_maxsize();
            $error = "file: {$arinfo["name"]} is larger ({$arinfo["size"]}) than allowed {$maxsize}";
            $this->add_error($error);
            return false;
        }
        return true;
    }//_is_validinfo

    private function _upload_single($arinfo)
    {
        //$this->logd($arinfo,"multiple.upload_single");
        $today = date("Ymd");
        $folderdomain = trim($this->post["folderdomain"]);
        $pathdest = "{$this->rootpath}/$folderdomain/{$today}";
        if(!file_exists($pathdest)) mkdir($pathdest, 0777, true);
        $filename = $this->_get_basename($arinfo["name"]);

        $now = date("His");
        $fileclean = $this->_get_cleaned($filename);
        $filefinal = "{$now}-{$fileclean}";
        $pathfinal = "{$pathdest}/$filefinal";
        if(is_file($pathfinal)) unlink($pathfinal);

        $r = $this->_get_saved($pathfinal, $arinfo);
        if(!$r) $this->add_error("An error ocurred while moving file: $filename to final dir");
        else
            $this->urls[] = $this->resources_url."/$folderdomain/$today/$filefinal";
    }

    private function _get_positions()
    {
        $names = $this->files["name"] ?? [];
        return array_keys($names);
    }

    private function _upload()
    {
        foreach ($this->positions as $ipos)
        {
            $arinfo = $this->_get_fileinfo($ipos);
            //$this->logd($arinfo,"arinfo de $ipos");
            if($this->_is_validinfo($arinfo))
                $this->_upload_single($arinfo);
        }
    }

    public function get_uploaded()
    {
        $this->_exceptions();
        $this->_upload();
        return $this->urls;
    }
}

/*
array (
  'files' =>
  array (
    'name' =>
    array (
      0 => 'trello1131785937.jpg',
      1 => 'trello-1412509703.jpg',
      2 => 'trello1857047292.jpg',
      3 => 'trello-290520641.jpg',
      4 => 'trello-995103245.jpg',
      5 => 'trello-1963122971.jpg',
    ),
    'type' =>
    array (
      0 => 'image/jpeg',
      1 => 'image/jpeg',
      2 => 'image/jpeg',
      3 => 'image/jpeg',
      4 => 'image/jpeg',
      5 => 'image/jpeg',
    ),
    'tmp_name' =>
    array (
      0 => '/private/var/folders/yt/g9dtf4cj40s6m5b4m_rzjz8m0000gn/T/php1Mazle',
      1 => '/private/var/folders/yt/g9dtf4cj40s6m5b4m_rzjz8m0000gn/T/phpkyw6ve',
      2 => '/private/var/folders/yt/g9dtf4cj40s6m5b4m_rzjz8m0000gn/T/phpfYJlf7',
      3 => '/private/var/folders/yt/g9dtf4cj40s6m5b4m_rzjz8m0000gn/T/phpj78oKw',
      4 => '/private/var/folders/yt/g9dtf4cj40s6m5b4m_rzjz8m0000gn/T/phpzQqq0m',
      5 => '/private/var/folders/yt/g9dtf4cj40s6m5b4m_rzjz8m0000gn/T/phplYzBmB',
    ),
    'error' =>
    array (
      0 => 0,
      1 => 0,
      2 => 0,
      3 => 0,
      4 => 0,
      5 => 0,
    ),
    'size' =>
    array (
      0 => 602682,
      1 => 203406,
      2 => 478562,
      3 => 597316,
      4 => 237435,
      5 => 220868,
    ),
  ),
)
*/