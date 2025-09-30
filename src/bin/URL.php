<?php


class URL
{

    public static function base(){
        $base_dir = str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
        $baseURL = (isset($_SERVER['HTTPS'])?"https":"http")."://{$_SERVER['HTTP_HOST']}{$base_dir}";
        return trim($baseURL,"/");
    }
    
    public static function root(){
        $baseURL = (isset($_SERVER['HTTPS'])?"https":"http")."://{$_SERVER['HTTP_HOST']}";
        return $baseURL;
    }

    public static function to($url){
        $url = trim($url,"/");
        
        // Si la URL es para archivos estáticos, usar la URL raíz en lugar del directorio base
        if (strpos($url, 'files/') === 0 || strpos($url, 'public/') === 0) {
            return URL::root()."/$url";
        }
        
        return URL::base()."/$url";
    }

    public static function getFull(){
        return (isset($_SERVER['HTTPS'])?"https":"http")."://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    }
}