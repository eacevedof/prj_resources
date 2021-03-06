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
use App\Services\UploadMultipleService;
use App\Services\UploadUrlService;
use App\Services\FilesService;

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

            $urls = $oServ->get_uploaded();
            $oJson->set_payload(["url"=>$urls,"warning"=>$oServ->get_errors()])->show();
        }
        catch (Exception $e)
        {
            $oJson->set_code(HelperJson::CODE_UNAUTHORIZED)->
            set_error([$e->getMessage()])->
            show(1);
        }
    }

    /**
     * ruta:
     *  <dominio>/upload/multiple
     */
    public function multiple()
    {
        $this->request_log();
        $oJson = new HelperJson();
        try{
            $oServ = new UploadMultipleService($this->get_post(),$this->get_files());

            $urls = $oServ->get_uploaded();
            $oJson->set_payload(["url"=>$urls,"warning"=>$oServ->get_errors()])->show();
        }
        catch (Exception $e)
        {
            $oJson->set_code(HelperJson::CODE_UNAUTHORIZED)->
            set_error([$e->getMessage()])->
            show(1);
        }
    }

    /**
     * ruta:
     *  <dominio>/upload/by-url
     */
    public function by_url()
    {
        $this->request_log();
        $oJson = new HelperJson();
        try{
            $oServ = new UploadUrlService($this->get_post());

            $urls = $oServ->get_uploaded();
            $oJson->set_payload(["url"=>$urls,"warning"=>$oServ->get_errors()])->show();
        }
        catch (Exception $e)
        {
            $oJson->set_code(HelperJson::CODE_UNAUTHORIZED)->
            set_error([$e->getMessage()])->
            show(1);
        }
    }

    /**
     * ruta:
     *  <dominio>/folders
     */
    public function folders()
    {
        $this->request_log();
        $oJson = new HelperJson();
        try{
            $oServ = new FilesService($this->get_post());
            $oJson->set_payload(["folders"=>$oServ->get_folders()])->show();
        }
        catch (Exception $e)
        {
            $oJson->set_code(HelperJson::CODE_UNAUTHORIZED)->
            set_error([$e->getMessage()])->
            show(1);
        }
    }

    /**
     * ruta:
     *  <dominio>/files
     */
    public function files()
    {
        $this->request_log();
        $oJson = new HelperJson();
        try{
            $oServ = new FilesService($this->get_post());
            $oJson->set_payload(["files"=>$oServ->get_files()])->show();
        }
        catch (Exception $e)
        {
            $oJson->set_code(HelperJson::CODE_UNAUTHORIZED)->
            set_error([$e->getMessage()])->
            show(1);
        }
    }    

    /**
     * ruta:
     *  <dominio>/remove
     */
    public function remove()
    {
        $this->request_log();
        $oJson = new HelperJson();
        try{
            $oServ = new FilesService($this->get_post());
            $oJson->set_payload(["urls"=>$oServ->remove(),"warning"=>$oServ->get_errors()])->show();
        }
        catch (Exception $e)
        {
            $oJson->set_code(HelperJson::CODE_UNAUTHORIZED)->
            set_error([$e->getMessage()])->
            show(1);
        }
    }
}//UploadController
