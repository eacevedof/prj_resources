<?php
//<project>\backend\src\routes\routes.php
//mapeo de rutas y controladores

return [   
    ["url"=>"/","controller"=>"App\Controllers\NotFoundController","method"=>"index"],
    ["url"=>"/upload","controller"=>"App\Controllers\UploadController","method"=>"index"],
    ["url"=>"/security/get-password","controller"=>"App\Controllers\Security\PasswordController","method"=>"index"],
    ["url"=>"/security/get-signature","controller"=>"App\Controllers\Security\SignatureController","method"=>"index"],
    ["url"=>"/security/is-valid-signature","controller"=>"App\Controllers\Security\SignatureController","method"=>"is_valid_signature"],
    ["url"=>"/security/login","controller"=>"App\Controllers\Security\LoginController","method"=>"index"],
    ["url"=>"/security/is-valid-token","controller"=>"App\Controllers\Security\LoginController","method"=>"is_valid_token"],
    ["url"=>"/404","controller"=>"App\Controllers\NotFoundController","method"=>"error_404"]
];