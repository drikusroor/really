<?php

class PageController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    public function index($slug) {

        $cwd = getcwd();
        $file = $cwd . '/pages/' . $slug . '.md';

        // if file exists, return as is
        if (file_exists($file)) {
            return file_get_contents($file);
        }

        // if file does not exist, return 404
        return '404';
    }

}