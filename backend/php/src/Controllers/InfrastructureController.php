<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @name App\Controllers\Security\UploadController
 * @file UploadController.php 1.0.0
 * @date 03-06-2020 18:17 SPAIN
 * @observations
 */
namespace App\Controllers;
use \Exception;
use TheFramework\Helpers\HelperJson;

use App\Services\UploadService;

class InfrastructureController extends  AppController
{
    public function __construct()
    {
        //comprueba post[resource-usertoken]
        $this->check_usertoken();
    }

    /**
     * ruta:
     *  <dominio>/get-max-upload-size
     */
    public function get_maxuploadsize()
    {
        //maxsize contempla 2 máximos: upload_max_filesize, post_max_size
        $size = UploadService::get_maxsize()."MB";
        $size = get_in_bytes($size);
        $this->log($size,"max size y bytes");
        (new HelperJson())->set_payload(["maxuploadsize"=>$size])->show();
    }
}