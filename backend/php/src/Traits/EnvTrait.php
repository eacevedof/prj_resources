<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @name App\Traits\EnvTrait
 * @file EnvTrait.php 1.0.0
 * @date 21-07-2020 19:00 SPAIN
 * @observations
 */
namespace App\Traits;

trait EnvTrait
{
    protected function get_env($key=null){ return ($key===null)?$_ENV:$_ENV[$key] ?? "";}

    protected function get_appenv($key){return $this->get_env("APP_{$key}");}

    protected function is_prod(){return $this->get_appenv("ENV")==="prod";}

    protected function is_test(){return $this->get_appenv("ENV")==="test";}

    protected function is_dev(){return $this->get_appenv("ENV")==="dev";}

    protected function is_local(){return $this->get_appenv("ENV")==="local";}

}//EnvTrait
