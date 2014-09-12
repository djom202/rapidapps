<?php
    class IndexController extends ControllerBase {
        public function index() {
            $params = array ();
            
            $this->view->show('index/index.php', $params);
        }
        
        public function json () {
            HTTP::JSON(200);
        }
        
        public function html () {
            echo "200 OK";
        }
    }
?>
