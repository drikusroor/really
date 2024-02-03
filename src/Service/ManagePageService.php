<?php

namespace Ainab\Really\Service;

use Parsedown;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Ainab\Really\Model\Frontmatter;
use Ainab\Really\Model\Page;
use Ainab\Really\Model\PageCollection;
use Ainab\Really\Model\PostInput;

class ManagePageService
{
    protected $twig;

    public function __construct()
    {
        $this->initializeTwig();
    }

    public function index()
    {
    }

    public function save(PostInput $postInput)
    {

        $frontmatter = new Frontmatter(
            $postInput->getTitle(),
            $postInput->getDate(),
            $this->getSlug($postInput),
            $postInput->getTags(),
            $postInput->getCategories(),
            $postInput->getDraft(),
            $postInput->getLayout(),
            $postInput->getAuthor(),
            $postInput->getExcerpt()
        );
        $slug = $frontmatter->getSlug();
        $content = $postInput->getContent();

        $this->saveMarkdownFile($slug, $frontmatter, $content);
        $html = $this->convertPageToHtml($frontmatter, $content);
        $this->safeWriteHtmlFile($slug, $html);
        $this->generateIndex();
        return $slug;
    }

    public function delete($slug)
    {
        $filename = $slug . '.md';
        $filepath = __DIR__ . '/../../content/pages/' . $filename;
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        $publicPath = $_SERVER['DOCUMENT_ROOT'] . '/public/' . $slug . '.html';
        if (file_exists($publicPath)) {
            unlink($publicPath);
        }
        $this->generateIndex();
    }

    public function rebuild()
    {
        $pages = $this->getPagesList();
        foreach ($pages as $page) {
            $html = $this->convertPageToHtml($page->getFrontmatter(), $page->getContent());
            $this->safeWriteHtmlFile($page->getFrontmatter()->getSlug(), $html);
        }

        $this->generateIndex();
    }

    private function getSlug(PostInput $postInput)
    {
        if ($postInput->getSlug()) {
            return $postInput->getSlug();
        } else {
            // Generate slug from title by sanitizing it, lowercasing it, and replacing spaces with hyphens
            return strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $postInput->getTitle()));
        }
    }

    private function saveMarkdownFile($slug, Frontmatter $frontmatter, string $content)
    {
        $filename = $slug . '.md';
        $filepath = __DIR__ . '/../../content/pages/' . $filename;

        if (false === file_put_contents($filepath, $frontmatter . $content)) {
            throw new \Exception('Failed to save file');
        }
    }

    private function convertPageToHtml(Frontmatter $frontmatter, string $content, $args = [])
    {
        $parsedown = new Parsedown();
        $safeHtml = $parsedown->text($content);

        return $this->twig->render('pages/page.html.twig', [
            'title' => $frontmatter->getTitle(),
            'date' => $frontmatter->getDate(),
            'content' => $safeHtml,
            ...$args
        ]);
    }

    private function convertHomepageToHtml(Frontmatter $frontmatter, $pages)
    {
        return $this->twig->render('pages/home.html.twig', [
            'title' => $frontmatter->getTitle(),
            'pages' => $pages
        ]);
    }

    private function safeWriteHtmlFile($slug, $html)
    {
        $publicPath = $_SERVER['DOCUMENT_ROOT'] . '/public/' . $slug . '.html';
        $file = fopen($publicPath, 'w');
        fwrite($file, $html);
        fclose($file);
    }

    private function generateIndex()
    {
        $pages = $this->getPagesList();
        $frontmatter = new Frontmatter(
            'Pages',
            null,
            'pages',
            null,
            null,
            true,
            'page',
            null,
            'This is an index of all pages.'
        );
        $html = $this->convertHomepageToHtml($frontmatter, $pages);
        $this->safeWriteHtmlFile('index', $html);
    }

    public function getPagesFilesList()
    {
        $pagesFiles = scandir(__DIR__ . '/../../content/pages');
        $pagesFiles = array_diff($pagesFiles, ['.', '..']);
        return $pagesFiles;
    }

    /**
     * @return Page[]
     */
    public function getPagesList()
    {
        $pagesFiles = $this->getPagesFilesList();

        $pages = [];
        foreach ($pagesFiles as $file) {
            $file = file_get_contents(__DIR__ . '/../../content/pages/' . $file);
            $page = Page::fromMarkdownString($file);

            $pages[] = $page;
        }


        return $pages;
    }

    public function getPage($slug)
    {
        $filename = $slug . '.md';
        $filepath = __DIR__ . '/../../content/pages/' . $filename;
        $file = file_get_contents($filepath);
        $page = Page::fromMarkdownString($file);

        return $page;
    }

    protected function initializeTwig()
    {
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        $this->twig = new Environment($loader);
    }
}
