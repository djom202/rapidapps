<?php

class Partial {

    public static function show($name, $_VARS = array()) {
        //$name es el nombre de nuestra plantilla, por ej, listado.php
        //$vars es el contenedor de nuestras variables, es un arreglo del tipo llave => valor, opcional.
        //Traemos una instancia de nuestra clase de configuracion.
        $config = Config::singleton();

        //Armamos la ruta a la plantilla
        $path = $config->get('viewsFolder') . $name;

        //Si no existe el fichero en cuestion, tiramos un 404
        if (file_exists($path) == false) {
            trigger_error('Template `' . $path . '` does not exist.', E_USER_NOTICE);
            return false;
        }

        //Finalmente, incluimos la plantilla.
        include($path);
    }

    /*
     * $vars:
     *  array that contain all elements to show in the table
     * 
     * $row:
     *  way to show the rows. This will be parser with :columname.
     *  Eg: if you array (vars) contain a var called name, and you want to create
     *      the table with this value, you should make this: <td>:name</td>, and
     *      all the rows will be parser with this element.
     */

    public static function fetchRows($vars = array(), $row = "", $start = 0, $count = 1000) {
        $result = "";
        for ($i = $start; $i < $start + $count && isset($vars[$i]); $i++) {
            $rows = $vars[$i];
            $tmp = $row;
            foreach ($rows as $key => $value) {
                if (!is_numeric($key)) {
                    $tmp = str_replace(":$key", $value, $tmp);
                }
            }
            if ($tmp !== "") {
                $result .= $tmp;
            }
        }

        return $result;
    }

    public static function fetchRows2($vars = array(), RowObject $row = null, $start = 0, $count = 1000) {
        $result = "";
        for ($i = $start; $i < $start + $count && isset($vars[$i]); $i++) {
            $result .= $row->fetch($vars[$i]);
        }

        return $result;
    }

    public static function arrayNames($vars, $exclude = array()) {
        $res = array();
        for ($i = 0; $i < count($vars); $i++) {
            $tmp = array();
            foreach ($vars[$i] as $key => $value) {
                if (!is_numeric($key) && !in_array($key, $exclude)) {
                    $tmp[$key] = $value;
                }
            }

            array_push($res, $tmp);
        }

        return $res;
    }

    public static function createResponse($header, $response) {
        $tmp = $header;
        $tmp['_response'] = $response;

        return $tmp;
    }

    public static function _empty($vars, $names) {
        foreach ($names as $name) {
            if (!empty($vars[$name])) {
                return false;
            }
        }

        return true;
    }

    public static function _filled($vars, $names) {
        foreach ($names as $name) {
            if (empty($vars[$name])) {
                return false;
            }
        }

        return true;
    }
    
    public static function _some ($vars, $names, $excludes = array ()) {
        foreach ($names as $name) {
            if (!empty($vars[$name])) {
                return true;
            }
        }
        
        foreach ($excludes as $name) {
            if (!empty($vars[$name])) {
                return false;
            }
        }

        return false;
    }

    public static function prefix($vars, $prefix) {
        $tmp = array();
        foreach ($vars as $key => $value) {
            $tmp["{$prefix}{$key}"] = urldecode($value);
        }

        return $tmp;
    }

    public static function loadPage($url) {
        $handler = curl_init($url);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handler);
        curl_close($handler);

        return $response;
    }

}

?>