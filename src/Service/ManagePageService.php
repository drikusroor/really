<?php

namespace Ainab\Really\Service;

use Parsedown;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

use Ainab\Really\Model\Frontmatter;

class ManagePageService {

    protected $twig;

    public function __construct() {
        $this->initializeTwig();
    }

    public function index() {
    }

    public function save(Frontmatter $frontmatter, string $content) {
        $slug = $this->getSlug($frontmatter);
        $this->saveMarkdownFile($slug, $frontmatter, $content);
        $html = $this->convertToHtml($frontmatter, $content);
        $this->safeWriteHtmlFile($slug, $html);
        $this->generateIndex();
        return $slug;
    }

    private function getSlug(Frontmatter $frontmatter) {
        if ($frontmatter->getSlug()) {
            return $frontmatter->getSlug();
        } else {
            return strtolower(preg_replace('/[^a-z0-9]+/', '', $frontmatter->getTitle())); // Sanitize title to generate slug
        }
    }

    private function saveMarkdownFile($slug, Frontmatter $frontmatter, string $content) {
        $filename = $slug . '.md';
        $filepath = __DIR__ . '/../../content/pages/' . $filename;

        if (false === file_put_contents($filepath, $frontmatter . $content)) {
            throw new \Exception('Failed to save file');
        }
    }

    private function convertToHtml(Frontmatter $frontmatter, string $content) {
        $parsedown = new Parsedown();
        $safeHtml = $parsedown->text($content);

        // Render the view using Twig
        return $this->twig->render('pages/page.html.twig', [
            'title' => $frontmatter->getTitle(),
            'date' => $frontmatter->getDate(),
            'content' => $safeHtml,
        ]);
    }

    private function safeWriteHtmlFile($slug, $html) {
        $publicPath = $_SERVER['DOCUMENT_ROOT'] . '/public/' . $slug . '.html';
        $file = fopen($publicPath, 'w');
        fwrite($file, $html);
        fclose($file);
    }

    private function generateIndex() {
        $pages = $this->getPagesList();
        $frontmatter = new Frontmatter('Pages', null, 'pages', null, null, true, 'page', null, 'This is an index of all pages.');
        $content = $this->getPagelistMarkdown($pages);
        $this->saveMarkdownFile('pages', $frontmatter, $content);
        $html = $this->convertToHtml($frontmatter, $content);
        $this->safeWriteHtmlFile('pages', $html);
    }

    private function getPagesList() {
        $pages = scandir(__DIR__ . '/../../content/pages');
        $pages = array_diff($pages, ['.', '..']);
        return array_values(array_map(function ($page) {
            return str_replace('.md', '', $page);
        }, $pages));
    }

    private function getPagelistMarkdown($pages): string {
        $markdown = '';

        foreach ($pages as $page) {
            $markdown .= '- [' . $page . '](/' . $page . ')
';
        }

        return $markdown;
    }

    protected function initializeTwig() {
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        $this->twig = new Environment($loader);
    }
}