<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CodeController
 *
 * @author edinson
 */
class CodeController extends ControllerBase {
    //put your code here
    private function returnIfNumber($number, $default) {
        if (!empty($number)) {
            if (is_numeric($number)) {
                return $number;
            } else {
                HTTP::JSON(400);
            }
        } else {
            return $default;
        }
    }
    
    public function generate () {
        if(!isset($_SESSION['idsuperuser'])) {
            HTTP::JSON(403);
        }
        
        $_filled = Partial::_filled($this->post, array (
            'idcupon', 'numero'
        ));
        
        if($_filled) {
            $n = $this->returnIfNumber($this->post['numero'], 0);
            $codigos = $this->getModel('codigo');
            $params = Partial::prefix($this->post, ':');
            $response = array ();
            $time = time();
            $cod = 0;
            
            for($i=0; $i<$n; $i++) {
                $md5 = md5("{$time}-{$i}");
                $params[':codigo'] = strtoupper(substr($md5, 0, 8));
                
                $result = $codigos->insert($params);
                
                if($result) {
                    array_push($response, $params[':codigo']);
                    $cod++;
                }
            }
            
            HTTP::JSON(Partial::createResponse(HTTP::Value(200), array (
                'ins' => $cod,
                'codes' => $response
            )));
        }
        
        HTTP::JSON(400);
    }
    
    public function disponibles () {
        if(!empty($this->get['idcupon']) && isset($_SESSION['idsuperuser'])) {
            $_cod = $this->getModel('codigos_disponibles')->select(array(
                ':idcupon' => $this->get['idcupon']
            ));
            
            $response = Partial::arrayNames($_cod);
            HTTP::JSON(Partial::createResponse(HTTP::Value(200), $response));
        }
        
        HTTP::JSON(403);
    }
    
    public function reservar () {
        $_filled = Partial::_filled($this->get, array (
            'token', 'idcupon'
        ));
        
        if($_filled) {
            $_cod = $this->getModel('codigos_disponibles')->select(array(
                ':idcupon' => $this->get['idcupon']
            ), ' LIMIT 1;');
            
            $usuario = $this->getModel('usuario_token')->select(array (
                ':token' => $this->get['token']
            ));
            
            if(count($_cod) > 0 && count($usuario) > 0) {
                $this->getModel('reservado')->insert(array (
                    ':idcodigo' => $_cod[0]['idcodigo'],
                    ':idusuario' => $usuario[0]['idusuario']
                ));
                
                $response = Partial::arrayNames($_cod, array('idcodigo'));
                HTTP::JSON(Partial::createResponse(HTTP::Value(200), $response[0]));
            }
            
            HTTP::JSON(304);
        }
        
        HTTP::JSON(400);
    }
}
