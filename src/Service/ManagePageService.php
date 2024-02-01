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

        $this->generateIndex();

        return $slug;
    }

    private function generateIndex() {
        $pages = scandir(__DIR__ . '/../../content/pages');
        $pages = array_diff($pages, ['.', '..']);
        $pages = array_map(function ($page) {
            return str_replace('.md', '', $page);
        }, $pages);
        $pages = array_values($pages);

        
        $frontmatter = new Frontmatter('Pages', null, 'pages', null, null, true, 'page', null, 'This is an index of all pages.');
        $content = $this->getPagelistMarkdown($pages);
        
        $filename = 'pages.md';
        $filepath = __DIR__ . '/../../content/pages/' . $filename;
        
        $file = fopen($filepath, 'w');
        fwrite($file, $frontmatter);
        fwrite($file, $content);
        fclose($file);

        return true;
    }

    private function getPagelistMarkdown($pages): string {
        $markdown = '';

        foreach ($pages as $page) {
            $markdown .= '- [' . $page . '](/' . $page . ')
';
        }

        return $markdown;
    }
}