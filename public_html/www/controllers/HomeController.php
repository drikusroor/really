<?php

class HomeController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        echo 'Hello World!';
    }

    public function about() {
        echo 'About Us';
    }

}