<?php
function appboot_loadenv()
{
    $arEnvs = ["prod" => ".env", "test" => ".env.test", "dev" => ".env.dev", "local" => ".env.local"];
    foreach ($arEnvs as $strenv) {
        $pathenv = PATH_ROOT . DS . $strenv;
        if (is_file($pathenv)) {
            $content = file_get_contents($pathenv);
            $lines = explode("\n", $content);

            $replace = [
                "%PATH_PUBLIC%" => PATH_PUBLIC, "%PATH_ROOT%" => PATH_ROOT,
                "%PATH_SRC%" => PATH_SRC, "%PATH_SRC_CONFIG%" => PATH_SRC_CONFIG
            ];

            foreach ($lines as $strline) {
                if (strstr($strline, "=")) {
                    $keyval = explode("=", $strline);
                    $key = trim($keyval[0]);
                    if ($key) {
                        $value = trim($keyval[1]) ?? "";
                        $value = str_replace(array_keys($replace), array_values($replace), $value);
                        $_ENV[$key] = $value;
                    }
                }
            }
        }//if is file
    }//foreach envs

    $_SERVER += $_ENV;
}