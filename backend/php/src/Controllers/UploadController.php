<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @name App\Controllers\Security\UploadController
 * @file UploadController.php 1.0.0
 * @date 03-06-2020 18:17 SPAIN
 * @observations
 */
namespace App\Controllers\Security;

use TheFramework\Helpers\HelperJson;
use App\Controllers\AppController;
use App\Services\Security\UploadService;

class UploadController extends AppController
{

    /**
     * ruta:
     *  <dominio>/upload
     */
    public function index()
    {
        $domain = $_SERVER["REMOTE_HOST"] ?? "*";
        //$this->logd($domain,"upload.index.domain");
        //$this->request_log();
        $oJson = new HelperJson();
        try{
            $oServ = new UploadService($domain,$this->get_files());
            $token = $oServ->get_token();
            $oJson->set_payload(["token"=>$token])->show();
        }
        catch (\Exception $e)
        {
            $oJson->set_code(HelperJson::CODE_UNAUTHORIZED)->
            set_error([$e->getMessage()])->
            show(1);
        }
    }

}//UploadController
