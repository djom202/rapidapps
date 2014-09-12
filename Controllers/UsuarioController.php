<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UsuarioController
 *
 * @author edinson
 */
class UsuarioController extends ControllerBase {
    //put your code here
    public function registrar () {
        if(Partial::_filled($this->post, array ('nombre', 'apellido', 'correo', 'fecha_nacimiento', 'sexo', 'clave'))) {
            $usuario = $this->getModel('usuario');
            $params = Partial::prefix($this->post, ':');
            $usuario->insert($params);
            
            if($usuario->lastID() > 0) {
                HTTP::JSON(Partial::createResponse(HTTP::Value(200), array (
                    'idusuario' => $usuario->lastID()
                )));
            }
            
            HTTP::JSON(424);
        }
        
        HTTP::JSON(400);
    }
    
    public function ver () {
        if(!empty($this->get['token'])) {
            $result = $this->getModel('usuario_token')->select(array (
                ':token' => $this->get['token']
            ));
            
            if(count($result) > 0) {
                $response = Partial::arrayNames($result, array());
                HTTP::JSON(Partial::createResponse(HTTP::Value(200), $response[0]));
            }
            
            HTTP::JSON(403);
        }
        
        HTTP::JSON(400);
    }
    
    public function editar () {
        if(!empty($this->get['token'])) {
            $result = $this->getModel('usuario_token')->select(array (
                ':token' => $this->get['token']
            ));
            
            if(count($result) > 0 && Partial::_empty($this->post, array ('idusuario'))) {
                $params = Partial::prefix($this->post, ':');
                
                $this->getModel('usuario')->update($result[0]['idusuario'], $params);
                HTTP::JSON(200);
            }
            
            HTTP::JSON(403);
        }
        
        HTTP::JSON(400);
    }
    
    public function reservas () {
        if(!empty ($this->get['token'])) {
            $usuario = $this->getModel('usuario_token')->select(array (
                ':token' => $this->get['token']
            ));
            
            if(count($usuario) > 0) {
                $result = $this->getModel('reservado')->select(array (
                    ':idusuario' => $usuario[0]['idusuario']
                ));
                
                $response = Partial::arrayNames($result, array ());
                HTTP::JSON(Partial::createResponse(HTTP::Value(200), $response));
            }
            
            HTTP::JSON(403);
        }
        
        HTTP::JSON(400);
    }
}
