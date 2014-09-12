<?php

class ErrorController extends ControllerBase {

    public function index() {
        HTTP::JSON(404);
    }

}

?>
