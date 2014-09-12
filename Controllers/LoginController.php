<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LoginController
 *
 * @author edinson
 */
class LoginController extends ControllerBase {
    //put your code here
    function login () {
        if (Partial::_filled($this->post, array ('user', 'pass'))) {
            $result = QueryFactory::query("
                SELECT idsuperuser, nombre, usuario 
                FROM superuser 
                WHERE usuario = :user 
                AND clave = MD5(:pass);", array(
                    ':user' => $this->post['user'],
                    ':pass' => $this->post['pass']
            ));

            if (count($result) == 1) {
                $_SESSION['idsuperuser'] = $result[0]['idsuperuser'];
                $_SESSION['idregister'] = $result[0]['idsuperuser'];
                $_SESSION['usuario'] = $result[0]['usuario'];
                $_SESSION['nombre'] = $result[0]['nombre'];

                $response = Partial::arrayNames($result);
                HTTP::JSON(Partial::createResponse(HTTP::Value(200), $response[0]));
            }
            
            HTTP::JSON(401);
        }
        
        HTTP::JSON(400);
    }
    
    function user () {
        $_filled = Partial::_filled($this->post, array (
            'user', 'pass'
        ));
        
        if($_filled) {
            $res = $this->getModel('usuario')->select(array (
                ':correo' => $this->post['user'],
                ':clave' => md5($this->post['pass'])
            ));
            
            if(count($res) > 0) {
                $token = md5($res[0]['correo']);
                
                HTTP::JSON(Partial::createResponse(HTTP::Value(200), array (
                    'token' => $token
                )));
            }
            
            HTTP::JSON(403);
        }
        
        HTTP::JSON(400);
    }
    
    function logout () {
        session_destroy();

        HTTP::JSON(200);
    }
}
