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
                
                $parsedown = new Parsedown();
                $content = file_get_contents($file);
                $content = $parsedown->text($content);

                return $content;
        }

        // if file does not exist, return 404
        return '404';
    }

}