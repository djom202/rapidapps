<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FormController
 *
 * @author edinson
 */
class FormController extends ControllerBase {
    public function _Always() {
        parent::_Always();
    }
    
    public function save () {
        if(isset($_SESSION['idsuperuser'])) {
            $_filled = Partial::_filled($this->post, array ('name', 'value'));
            
            if($_filled) {
                $params = Partial::prefix($this->post, ':');
                $params[':idsuperuser'] = $_SESSION['idsuperuser'];
                $this->getModel('form')->insertorupdate($params);
                
                HTTP::JSON(200);
            }
            
            HTTP::JSON(400);
        }
        
        HTTP::JSON(401);
    }
    
    public function get () {
        if(isset($_SESSION['idregister'])) {
            $params = array (
                ':idsuperuser' => $_SESSION['idregister']
            );
            
            if(!isset($_SESSION['idsuperuser'])) {
                $params[':status'] = 'Publicado';
            }
            
            if(!empty($this->get['idform'])) {
                $params[':idform'] = $this->get['idform'];
                
                $result = $this->getModel('form')->select($params);

                $response = Partial::arrayNames($result, array ('idsuperuser'));
                HTTP::JSON(Partial::createResponse(HTTP::Value(200), $response[0]));
            }
            
            $result = $this->getModel('form')->select($params);
            
            $response = Partial::arrayNames($result, array ('idsuperuser'));
            HTTP::JSON(Partial::createResponse(HTTP::Value(200), $response));
            
            HTTP::JSON(400);
        }
        
        HTTP::JSON(401);
    }
    
    public function remove () {
        if(isset($_SESSION['idsuperuser'])) {
            if(!empty($this->delete['idform'])) {
                $this->getModel('form')->delete($this->delete['idform']);
                
                HTTP::JSON(200);
            }
            
            HTTP::JSON(400);
        }
        
        HTTP::JSON(401);
    }
    
    public function status () {
        if(isset($_SESSION['idsuperuser'])) {
            $_filled = Partial::_filled($this->put, array ('status', 'idform'));
            
            if($_filled) {
                $params = Partial::prefix($this->put, ':');
                $params[':idsuperuser'] = $_SESSION['idsuperuser'];
                $this->getModel('form')->insertorupdate($params);
                
                HTTP::JSON(200);
            }
            
            HTTP::JSON(400);
        }
        
        HTTP::JSON(401);
    }
}
