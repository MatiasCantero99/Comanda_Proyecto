<?php

class Validador
{
    public static function ValidarTipo($tipo){
        if(self::ValidarSTR($tipo)){
            $tipoMinuscula = strtolower($tipo);
            if($tipoMinuscula == "bartender" || $tipoMinuscula == "cervecero" || $tipoMinuscula == "mozo" || $tipoMinuscula == "cocinero" || $tipoMinuscula == "socio"){
                return true;
            }
        }
        return false;
    }
    public static function ValidarTipoProducto($tipo){
        if(self::ValidarSTR($tipo)){
            $tipoMinuscula = strtolower($tipo);
            if($tipoMinuscula == "bartender" || $tipoMinuscula == "cervecero"  || $tipoMinuscula == "cocinero"){
                return true;
            }
        }
        return false;
    }

    public static function ValidarTipoEspecifico($tipo,$tipoBuscar){
        if(self::ValidarSTR($tipo)){
            $tipoMinuscula = strtolower($tipo);
            if($tipoMinuscula == $tipoBuscar ){
                return true;
            }
        }
        return false;
    }
    
    public static function ValidarTalla($tipo){
        if(self::ValidarSTR($tipo)){
            $tipoMinuscula = strtolower($tipo);
            if($tipoMinuscula == "l" || $tipoMinuscula == "m" || $tipoMinuscula == "s"){
                return true;
            }
        }
        return false;
    }
    public static function ValidarSTR($dato){
        if(is_string($dato) && !is_numeric($dato))
        {
            return true;
        }
        return false;
    }
    public static function ValidarInt($dato){
        if (is_numeric($dato))
        {
            return true;
        }
        return false;
    }
    public static function esEmail($mail)
    {
        if (filter_var($mail, FILTER_VALIDATE_EMAIL) !== false) 
        {
            return true;
        } 
        else 
        {
            return false;
        }
    }
}