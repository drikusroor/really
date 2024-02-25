<?php

namespace Ainab\Really\Service;

use Parsedown;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Ainab\Really\Model\Frontmatter;
use Ainab\Really\Model\Page;
use Ainab\Really\Model\ContentInput;
use ContentType;

class ManageContentService
{
    protected $twig;

    public function __construct()
    {
        $this->initializeTwig();
    }

    public function index()
    {
    }

    public function save(ContentInput $contentInput)
    {
        $frontmatter = new Frontmatter(
            $contentInput->getContentType(),
            $contentInput->getTitle(),
            $contentInput->getDate(),
            $this->getSlug($contentInput),
            $contentInput->getTags(),
            $contentInput->getCategories(),
            $contentInput->getDraft(),
            $contentInput->getLayout(),
            $contentInput->getAuthor(),
            $contentInput->getExcerpt()
        );
        $slug = $frontmatter->getSlug();
        $content = $contentInput->getContent();

        $this->saveMarkdownFile($slug, $frontmatter, $content);
        $html = $this->convertContentToHtml($frontmatter, $content);
        $this->safeWriteHtmlFile($slug, $html);
        $this->generateIndex();
        return $slug;
    }

    public function delete($slug)
    {
        $filename = $slug . '.md';
        $filepath = __DIR__ . '/../../db/pages/' . $filename;
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        $publicPath = $_SERVER['DOCUMENT_ROOT'] . '/public/' . $slug . '.html';
        if (file_exists($publicPath)) {
            unlink($publicPath);
        }
        $this->generateIndex();
    }

    public function preview(ContentInput $contentInput)
    {
        $frontmatter = new Frontmatter(
            $contentInput->getContentType(),
            $contentInput->getTitle(),
            $contentInput->getDate(),
            $this->getSlug($contentInput),
            $contentInput->getTags(),
            $contentInput->getCategories(),
            $contentInput->getDraft(),
            $contentInput->getLayout(),
            $contentInput->getAuthor(),
            $contentInput->getExcerpt()
        );
        $html = $this->convertContentToHtml($frontmatter, $contentInput->getContent(), ['preview' => true]);
        return $html;
    }

    public function rebuild()
    {
        $pages = $this->getContentList();
        foreach ($pages as $page) {
            $html = $this->convertContentToHtml($page->getFrontmatter(), $page->getContent());
            $this->safeWriteHtmlFile($page->getFrontmatter()->getSlug(), $html);
        }

        $this->generateIndex();
    }

    private function getSlug(ContentInput $contentInput)
    {
        if ($contentInput->getSlug()) {
            return $contentInput->getSlug();
        } else {
            // Generate slug from title by sanitizing it, lowercasing it, and replacing spaces with hyphens
            return strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $contentInput->getTitle()));
        }
    }

    private function saveMarkdownFile($slug, Frontmatter $frontmatter, string $content)
    {
        $filename = $slug . '.md';
        $contentType = $frontmatter->getContentType();
        $filepath = __DIR__ . '/../../db/' . $contentType . '/' . $filename;

        if (false === file_put_contents($filepath, $frontmatter . $content)) {
            throw new \Exception('Failed to save file');
        }
    }

    private function convertContentToHtml(Frontmatter $frontmatter, string $content, $args = [])
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
        
        $directory = dirname($publicPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        
        $file = fopen($publicPath, 'w');
        fwrite($file, $html);
        fclose($file);
    }

    private function generateIndex()
    {
        $pages = $this->getContentList();
        $frontmatter = new Frontmatter(
            ContentType::PAGE,
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

    public function getContentFilesList()
    {
        $pagesFiles = scandir(__DIR__ . '/../../db/content/');
        $pagesFiles = array_diff($pagesFiles, ['.', '..']);
        
        return $pagesFiles;
    }

    /**
     * @return Page[]
     */
    public function getContentList()
    {
        $pagesFiles = $this->getContentFilesList();

        $pages = [];
        foreach ($pagesFiles as $file) {
            $file = file_get_contents(__DIR__ . '/../../db/content/' . $file);
            $page = Page::fromMarkdownString($file);

            $pages[] = $page;
        }


        return $pages;
    }

    public function getContentItem($slug)
    {
        $filename = $slug . '.md';
        $filepath = __DIR__ . '/../../db/content/' . $filename;
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
