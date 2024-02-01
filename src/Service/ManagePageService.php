<?php

namespace Ainab\Really\Service;

use Ainab\Really\Model\Frontmatter;

class ManagePageService {

    public function __construct() {
    }

    public function index() {
    }

    public function save(Frontmatter $frontmatter, string $content) {

        if ($frontmatter->getSlug()) {
            $slug = $frontmatter->getSlug();
        } else {
            $date = \date('Y-m-d');
            $slug = $date . '-' . strtolower($frontmatter->getTitle());
        }
        
        $filename = $slug . '.md';
        $filepath = __DIR__ . '/../../content/pages/' . $filename;

        $file = fopen($filepath, 'w');
        fwrite($file, $frontmatter);
        fwrite($file, $content);
        fclose($file);

        return $slug;
    }

}