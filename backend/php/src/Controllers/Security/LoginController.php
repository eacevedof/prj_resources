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

use App\Services\Security\LoginMiddleService;
use App\Services\Security\LoginService;
use TheFramework\Helpers\HelperJson;
use App\Controllers\AppController;

class LoginController extends AppController
{

    /**
     * ruta:
     *  <dominio>/security/login
     */
    public function index()
    {
        $domain = $_SERVER["REMOTE_HOST"] ?? "*";
        $this->logd($domain,"login.index.domain");
        $this->request_log();
        $oJson = new HelperJson();
        try{
            //post para login: user, password
            $oServ = new LoginService($domain,$this->get_post());
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
    /**
     * Para servidores intermediarios
     * El serv tiene que hacer un forward en POST de remoteip y remotehost
     * ruta:
     *  <dominio>/security/login-middle
     */
    public function middle()
    {
        $oJson = new HelperJson();
        try{
            $oServ = new LoginMiddleService($this->get_post());
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

    /**
     * ruta:
     *  <dominio>/security/is-valid-token
     */
    public function is_valid_token()
    {
        $domain = $_SERVER["REMOTE_HOST"] ?? "*";
        $this->logd($domain,"login.is_valid_token.domain");
        $oJson = new HelperJson();
        try{
            $token = $this->get_post(self::KEY_RESOURCE_USERTOKEN);
            $this->logd($token,"login.is_valid_token.header");
            $this->logd("domain: $domain, token: $token");
            $oServ = new LoginService($domain);
            $oServ->is_valid($token);
            $oJson->set_payload(["isvalid"=>true])->show();
        }
        catch (\Exception $e)
        {
            $oJson->set_code(HelperJson::CODE_FORBIDDEN)->
            set_error([$e->getMessage()])->
            show(1);
        }
    }
}//UploadController
