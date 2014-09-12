<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ScreensController
 *
 * @author edinson
 */
class ScreensController extends ControllerBase {
    public function _Always() {
        parent::_Always();
    }
    
    function get () {
        if(isset($_SESSION['idsuperuser'])) {
            if (!empty($this->get['idform'])) {
                
                $res = $this->getModel('form')->select(array (
                    ':idsuperuser' => $_SESSION['idsuperuser'],
                    ':idform' => $this->get['idform']
                ));
                
                if(count($res) > 0) {
                    $conn = new Mongo('localhost');
                    
                    $db = $conn->rapidapps;
                    $collection = $db->screens;
                    $cursor = $collection->find(array (
                        'idform' => intval($this->get['idform'])
                    ));
                    
                    $cursor->sort(array('date' => -1));
                    
                    $tmp = array ();
                    foreach ($cursor as $obj) {
                        array_push($tmp, array (
                            '_id' => (string)$obj['_id'],
                            'consecutivo' => $obj['consecutivo'],
                            'screens' => $obj['screens'],
                            'name' => $obj['name'],
                            'user' => $obj['user'],
                            'date' => $obj['date'],
                            'idform' => $obj['idform']
                        ));
                    }
                    
                    $result = Partial::arrayNames($tmp, array ());
                    
                    HTTP::JSON(Partial::createResponse(HTTP::Value(200), $result));
                }
                
                HTTP::JSON(404);
            }
            
            HTTP::JSON(400);
        }
        
        HTTP::JSON(401);
    }
    
    function save () {
        if(isset($_SESSION['idregister'])) {
            if (!empty($this->post['idform'])) {
                $form = $this->getModel('form');
                
                $res = $form->select(array (
                    ':idsuperuser' => $_SESSION['idregister'],
                    ':idform' => $this->post['idform']
                ));
                
                if(count($res) > 0) {
                    $conn = new Mongo('localhost');
                    
                    $db = $conn->rapidapps;
                    $collection = $db->screens;
                    //$collection->find();
                    
                    $consecutivo = $res[0]['consecutivo'] + 1;
                    $screens = json_decode($this->post['screens'], true);
                    $form->update($this->post['idform'], array (
                        ':consecutivo' => $consecutivo
                    ));
                    
                    $collection->insert(array (
                        'idform' => intval($this->post['idform']),
                        'name' => $res[0]['name'],
                        'user' => $_SESSION['usuario'],
                        'date' => date('Y-m-d H:i:s'),
                        'consecutivo' => $consecutivo,
                        'screens' => $screens
                    ));
                    
                    if($res[0]['emailers'] != "") {
                        $to = $res[0]['emailers'];
                        $subject = $consecutivo . ' de ' . $res[0]['name'];

                        $message = '<html><head><title>' . $consecutivo . ' de ' . $res[0]['name'] . '</title></head>';
                        $message .= '<body>';
                        
                        foreach($screens as $sname => $screen) {
                            $message .= '<table><caption>' . $sname . '</caption>';
                            
                            foreach($screen as $key => $value) {
                                $message .= '<tr><td>' . $key . '</td>';
                                
                                if(preg_match("/^data:image\/.+;base64,.*/", $value, $match)) {
                                    $message .= '<td><img src="' . $value . '"></td></tr>';
                                } elseif (preg_match("/^\([\d\-\.]+,[\d\-\.]+\)$/", $value, $match)) {
                                    $message .= '<td><img src="http://maps.googleapis.com/maps/api/staticmap?center=' . substr($value, 1, strlen($value)-2) . '&zoom=15&size=200x150&sensor=false"></td></tr>';
                                } else {
                                    $message .= '<td>' . $value . '</td>';
                                }
                            }
                            
                            $message .= '</table>';
                        }
                        
                        $message .= '<p>Gambas</p></body></html>';

                        // Always set content-type when sending HTML email
                        $headers = "MIME-Version: 1.0" . "\r\n";
                        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                        // More headers
                        $headers .= 'From: <no-reply@gambas.co>' . "\r\n";

                        mail($to,$subject,$message,$headers);
                    }
                    
                    HTTP::JSON(200);
                }
                
                HTTP::JSON(404);
            }
            
            HTTP::JSON(400);
        }
        
        HTTP::JSON(401);
    }
    
    function remove () {
        if(isset($_SESSION['idsuperuser'])) {
            if (Partial::_filled($this->delete, array ('_id'))) {
                $conn = new Mongo('localhost');
                
                $db = $conn->rapidapps;
                $collection = $db->screens;
                $collection->remove(array (
                    '_id' => new MongoID($this->delete['_id'])
                ));
                
                HTTP::JSON(200);
            }
            
            HTTP::JSON(400);
        }
        
        HTTP::JSON(401);
    }
}
