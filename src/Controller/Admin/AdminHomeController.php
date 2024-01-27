<?php
namespace Ainab\Really\Controller\Admin;

use Ainab\Really\Controller\BaseController;

class AdminHomeController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        echo $this->twig->render('admin/index.html.twig');
    }

}