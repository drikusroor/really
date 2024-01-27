<?php
namespace Ainab\Really\Controller;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class BaseController {

    protected $twig;

    public function __construct() {
        $this->initializeTwig();
    }

    protected function initializeTwig() {
        // Define the path to your Twig templates
        $loader = new FilesystemLoader(__DIR__ . '/../templates');

        // Create the Twig environment with optional settings
        $this->twig = new Environment($loader, [
            // 'cache' => '/path/to/compilation_cache',
            // Other environment options can be set here
        ]);
    }

}