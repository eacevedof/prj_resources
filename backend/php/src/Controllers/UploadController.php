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

use TheFramework\Helpers\HelperJson;
use App\Controllers\AppController;
use App\Services\UploadService;

class UploadController extends AppController
{

    public function __construct()
    {
        //comprueba post[resource-usertoken]
        $this->check_usertoken();
    }

    /**
     * ruta:
     *  <dominio>/upload
     */
    public function index()
    {
        $this->request_log();
        $oJson = new HelperJson();
        try{
            $oServ = new UploadService($this->get_post(),$this->get_files());

            $token = $oServ->get_uploaded();
            $oJson->set_payload(["url"=>$token,"warning"=>$oServ->get_errors()])->show();
        }
        catch (\Exception $e)
        {
            $oJson->set_code(HelperJson::CODE_UNAUTHORIZED)->
            set_error([$e->getMessage()])->
            show(1);
        }
    }

    /**
     * ruta:
     *  <dominio>/get-max-upload-size
     */
    public function get_maxuploadsize(){
        $size = UploadService::get_maxsize()."MB";
        $size = get_in_bytes($size);
        $this->log($size,"max size y bytes");
        (new HelperJson())->set_payload(["maxuploadsize"=>$size])->show();
    }

}//UploadController
