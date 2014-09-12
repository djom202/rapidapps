<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserController
 *
 * @author edinson
 */
class UserController extends ControllerBase {
    //put your code here
    function login () {
        if(Partial::_filled($this->post, array ('user', 'pass'))) {
            $res = $this->getModel('user')->select(array (
                ':user' => $this->post['user'],
                ':pass' => md5($this->post['pass'])
            ));
            
            if(count($res) > 0) {
                $result = Partial::arrayNames($res, array ('pass'));
                
                $_SESSION['idregister'] = $res[0]['idsuperuser'];
                $_SESSION['iduser'] = $res[0]['iduser'];
                $_SESSION['usuario'] = $res[0]['user'];
                $_SESSION['nombre'] = $res[0]['name'];
                
                HTTP::JSON(Partial::createResponse(HTTP::Value(200), $result[0]));
            }
            
            HTTP::JSON(403);
        }
        
        HTTP::JSON(400);
    }
    
    function logout () {
        session_destroy();
        
        HTTP::JSON(200);
    }
    
    function active () {
        if(isset($_SESSION['idsuperuser'])) {
            $result = $this->getModel('superuser')->select(array (
                ':idsuperuser' => $_SESSION['idsuperuser']
            ));

            $response = Partial::arrayNames($result, array (
                'pass'
            ));

            HTTP::JSON(Partial::createResponse(HTTP::Value(200), $response[0]));
        }
        
        HTTP::JSON(401);
    }
}
