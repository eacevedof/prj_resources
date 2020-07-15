<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @name App\Controllers\Apify\SignatureController 
 * @file SignatureController.php 1.0.0
 * @date 27-06-2019 18:17 SPAIN
 * @observations
 */
namespace App\Controllers\Security;

use TheFramework\Helpers\HelperJson;
use App\Controllers\AppController;
use App\Services\Security\SignatureService;

class SignatureController extends AppController
{

    /**
     * ruta:
     *  <dominio>/security/get-signature
     */
    public function index()
    {
        $domain = $_SERVER["REMOTE_HOST"] ?? "*";
        $oJson = new HelperJson();
        try{
            $oServ = new SignatureService($domain,$this->get_post());
            $token = $oServ->get_token();
            $oJson->set_payload(["result"=>$token])->show();
        }
        catch (\Exception $e)
        {
            $oJson->set_code(HelperJson::CODE_UNAUTHORIZED)->
            set_error([$e->getMessage()])->
            show(1);
        }

    }//index

    /**
     * ruta:
     *  <dominio>/security/is-valid-signature
     */
    public function is_valid_signature()
    {
        $this->check_signature();
        (new HelperJson())->set_payload(["result"=>true])->show();
    }//index
    
}//SignatureController
