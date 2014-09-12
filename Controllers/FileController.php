<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FileController
 *
 * @author edinson
 */
class FileController extends ControllerBase {
            
    public function _Always() {
        if(!isset($_SESSION['idsuperuser'])) {
            HTTP::JSON(401);
        }
    }
    
    function scan () {
        if(isset($this->get['dir'])) {
            $dir = $this->config->get('BaseDir') . urldecode($this->get['dir']);
        }
        
        if(is_dir($dir)) {
            $files = scandir($dir);

            $result = array (
                'path' => $dir,
                'scan' => array ()
            );
            
            foreach ($files as $file) {
                if($file != '.' && $file != '..') {
                    array_push($result['scan'], array (
                        'name' => $file,
                        'type' => (is_file("$dir/$file"))? 'file' : 'folder'
                    ));
                }
            }
            
            HTTP::JSON(Partial::createResponse(HTTP::Value(200), $result));
        }
        
        HTTP::JSON(400);
    }
    
    function add () {
        if(!empty ($this->get['dir'])) {
            
            if(mkdir($this->config->get('BaseDir') . urldecode($this->get['dir']))) {
                HTTP::JSON(200);
            }
            HTTP::JSON(403);
        }
        
        HTTP::JSON(400);
    }
    
    function delete () {
        if(!empty($this->get['dir'])) {
            $dir = $this->config->get('BaseDir') . urldecode($this->get['dir']);
            
            if(is_file($dir)) {
                if(unlink($dir)) {
                    HTTP::JSON(200);
                }
                
                HTTP::JSON(403);
            }
            
            if(is_dir($dir)) {
                if(rmdir($dir)) {
                    HTTP::JSON(200);
                }
                
                HTTP::JSON(403);
            }
        }
        
        HTTP::JSON(400);
    }
    
    function upload () {
        if(isset($this->post['dir']) && isset($this->files['file'])) {
            $types = array();
            
            if(!in_array($this->files['file']['type'], $types)) {
                $uploaddir = $this->config->get('BaseDir') . $this->post['dir'];
                $uploadfile = $uploaddir . basename($this->files['file']['name']);

                if (move_uploaded_file($this->files['file']['tmp_name'], $uploadfile)) {
                    HTTP::JSON(200);
                }
            }
            
            HTTP::JSON(403);
        }
        
        HTTP::JSON(400);
    }
    
    function thumb () {
        if(!empty($this->get['dir'])) {
            $dir = $this->config->get('BaseDir') . urldecode($this->get['dir']);
            if(is_file($dir)) {
                $ext = array ('png', 'jpg', 'jpeg', 'gif', 'bmp');
                $tmp = explode('.', $dir);
                $source_image = null;
                
                if(count($tmp) > 0) {
                    if(in_array($tmp[count($tmp)-1], $ext)) {
                        $extension = $tmp[count($tmp)-1];
                        
                        switch ($extension) {
                            case 'jpg':
                                $source_image = imagecreatefromjpeg($dir);
                                break;
                            case 'jpeg':
                                $source_image = imagecreatefromjpeg($dir);
                                break;
                            case 'png':
                                $source_image = imagecreatefrompng($dir);
                                break;
                            case 'gif':
                                $source_image = imagecreatefromgif($dir);
                                break;
                            case 'bmp':
                                $source_image = imagecreatefrombmp($dir);
                                break;
                        }
                    }
                }
                
                if($source_image == null) {
                    $source_image = imagecreatefrompng($this->config->get('BaseDir') . "../file.png");
                    $extension = 'png';
                }
                
                $SW = imagesx($source_image);
                $SH = imagesy($source_image);

                if($SW > $SH) {
                    $DW = 90;
                    $DH = 90*($SH/$SW);
                } else {
                    $DW = 90*($SW/$SH);
                    $DH = 90;
                }
                
                $dest_image = imagecreatetruecolor($DW, $DH);

                $white = imagecolorallocate($dest_image, 255, 255, 255);
                imagefilledrectangle($dest_image, 0, 0, $DW, $DH, $white);

                imagecopyresampled($dest_image, $source_image, 0, 0, 0, 0, $DW, 
                                        $DH, $SW, $SH);
                
                header("Content-Type: image/jpeg");
                imagejpeg($dest_image);
                
                exit();
            }
        }
        
        HTTP::JSON(400);
    }
    
    function info () {
        if(!empty($this->get['dir'])) {
            $file = $this->config->get('BaseDir') . urldecode($this->get['dir']);
            if(is_file($file)) {
                $info = stat($this->config->get('BaseDir') . urldecode($this->get['dir']));
                
                $result = Partial::arrayNames(array ($info));
                HTTP::JSON(Partial::createResponse(HTTP::Value(200), $result[0]));
            }
        }
        
        HTTP::JSON(400);
    }
}

?>
