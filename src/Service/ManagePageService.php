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

        if ($frontmatter->getSlug()) {
            $slug = $frontmatter->getSlug();
        } else {
            $slug = strtolower($frontmatter->getTitle());
        }

        $filename = $slug . '.md';
        $filepath = __DIR__ . '/../../content/pages/' . $filename;

        $file = fopen($filepath, 'w');
        fwrite($file, $frontmatter);
        fwrite($file, $content);
        fclose($file);

        // now convert the page to html using a twig template
        $html = $this->convertToHtml($frontmatter, $content);

        // save the html to a file in the public directory
        

        $this->generateIndex();

        return $slug;
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

    private function extractFrontmatter(string $fileContents)
    {
        // Initialize the result with empty frontmatter and content
        $result = [
            'frontmatter' => [],
            'content' => ''
        ];

        // Regular expression to match the frontmatter and content
        $pattern = '/^---\s*(.*?)\s*---\s*(.*)/s';

        // Perform the regex match
        if (preg_match($pattern, $fileContents, $matches)) {
            // Split the frontmatter into lines
            $frontmatterLines = explode("\n", trim($matches[1]));

            // Parse each line of the frontmatter into key-value pairs
            foreach ($frontmatterLines as $line) {
                if (strpos($line, ':') !== false) {
                    list($key, $value) = explode(':', $line, 2);
                    $result['frontmatter'][trim($key)] = trim($value);
                }
            }

            // Assign the remaining content
            $result['content'] = $matches[2];
        } else {
            // If no frontmatter is found, assume the entire file is content
            $result['content'] = $fileContents;
        }

        return $result;
    }

    private function safeGet($array, $key, $default = '')
    {
        return isset($array[$key]) ? $array[$key] : $default;
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