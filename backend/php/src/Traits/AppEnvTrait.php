<?php
/**
 * @author Eduardo Acevedo Farje.
 * @link www.eduardoaf.com
 * @name App\Traits\AppEnvTrait
 * @file AppEnvTrait.php 1.0.0
 * @date 16-07-2020 19:00 SPAIN
 * @observations
 */
namespace App\Traits;

trait AppEnvTrait
{
    public function get_env($key=null)
    {
        if($key===null) return $_ENV;
        return $_ENV[$key] ?? "";
    }
    
}//AppLogTrait
