<?php

namespace Ainab\Really\Controller;

use Parsedown;

class PageController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index($slug)
    {
        $cwd = getcwd();
        $file = $cwd . '/../content/pages/' . $slug . '.md';

        if (file_exists($file)) {
            $parsedown = new Parsedown();
            $rawContent = file_get_contents($file);
            $extracted = $this->extractFrontmatter($rawContent);
            $frontmatter = $extracted['frontmatter'];
            $content = $extracted['content'];

            $safeHtml = $parsedown->text($content);

            // Render the view using Twig
            echo $this->twig->render('pages/page.html.twig', [
                'title' => $this->safeGet($frontmatter, 'title', 'Untitled'),
                'description' => $this->safeGet($frontmatter, 'description', ''),
                'date' => $this->safeGet($frontmatter, 'date', ''),
                'content' => $safeHtml,
            ]);

            return;
        }

        // Handle 404
        echo $this->twig->render('errors/404.html.twig');
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
}
