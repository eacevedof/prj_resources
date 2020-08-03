<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @name App\Controllers\Apify\PasswordController 
 * @file PasswordController.php 1.0.0
 * @date 27-06-2019 18:17 SPAIN
 * @observations
 */
namespace App\Controllers\Security;

use TheFramework\Helpers\HelperJson;
use App\Controllers\AppController;
use App\Services\Security\SignatureService;

class PasswordController extends AppController
{

    /**
     * ruta:
     *  <dominio>/security/get-password
     */
    public function index()
    {
        $oJson = new HelperJson();
        try{
            $domain = $this->get_domain(); //excepcion
            $oServ = new SignatureService($domain,$this->get_post());
            $token = $oServ->get_password();
            $oJson->set_payload(["result"=>$token])->show();
        }
        catch (\Exception $e)
        {
            $oJson->set_code(HelperJson::CODE_UNAUTHORIZED)->
            set_error([$e->getMessage()])->
            show(1);
        }

    }//index

}//PasswordController
