<?php
namespace Ainab\Really\Controller;

use Parsedown;
use HTMLPurifier;
use HTMLPurifier_Config;

class PageController extends BaseController {

    public function __construct() {
        parent::__construct();
    }

    public function index($slug) {

        $cwd = getcwd();
        $file = $cwd . '/../content/pages/' . $slug . '.md';

        // if file exists, return as is
        if (file_exists($file)) {
                
                // Parse Markdown
                $parsedown = new Parsedown();
                $content = file_get_contents($file);
                $content = $parsedown->text($content);

                // Sanitize HTML
                $config = HTMLPurifier_Config::createDefault();
                $purifier = new HTMLPurifier($config);
                $safeHtml = $purifier->purify($content);

                echo $safeHtml;
        }

        // if file does not exist, return 404
        echo '404';

        return '404';
    }

}