<?php
namespace Ainab\Really\Controller;

use Parsedown;

class PageController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    public function index($slug) {
        $cwd = getcwd();
        $file = $cwd . '/../content/pages/' . $slug . '.md';

        if (file_exists($file)) {
            $parsedown = new Parsedown();
            $content = file_get_contents($file);
            $safeHtml = $parsedown->text($content);

            // Render the view using Twig
            echo $this->twig->render('pages/page.html.twig', [
                'title' => 'Your Page Title',
                'content' => $safeHtml,
            ]);

            return;
        }

        // Handle 404
        echo $this->twig->render('errors/404.html.twig');
    }

}