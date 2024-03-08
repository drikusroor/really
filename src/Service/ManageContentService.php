<?php

namespace Ainab\Really\Service;

use Ainab\Really\Model\ContentFile;
use Parsedown;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Ainab\Really\Model\Frontmatter;
use Ainab\Really\Model\Page;
use Ainab\Really\Model\ContentInput;
use Ainab\Really\Model\ContentType;
use Ainab\Really\Model\Post;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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
        $filename = $contentInput->getFilepath();
        $slug = $frontmatter->getSlug();
        $content = $contentInput->getContent();

        $this->saveMarkdownFile($filename, $frontmatter, $content);
        $html = $this->convertContentToHtml($frontmatter, $content);
        $this->safeWriteHtmlFile($filename, $html);
        $this->generateIndex();
        return $slug;
    }

    public function delete(string $filename)
    {
        $filepath = __DIR__ . '/../../db/content/' . $filename;
        if (file_exists($filepath)) {
            unlink($filepath);
        }
        $file = file_get_contents($filepath);
        $frontmatter = Frontmatter::fromMarkdownString($file);
        $slug = $frontmatter->getSlug();
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

    private function saveMarkdownFile(string $filepath, Frontmatter $frontmatter, string $content)
    {
        $filepath = __DIR__ . '/../../db/content/' . $filepath;

        // Create directory if it doesn't exist
        $directory = dirname($filepath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

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
            ContentType::PAGE->value,
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

    public function getContentFilesList(ContentType $contentTypeFilter = null)
    {
        $contentFiles = [];
        $directory = new RecursiveDirectoryIterator(__DIR__ . '/../../db/content/');
        $iterator = new RecursiveIteratorIterator($directory);

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filename = $file->getFilename();
                if (pathinfo($filename, PATHINFO_EXTENSION) === 'md') {
                    $filepath = $file->getPathname();
                    $path = str_replace(__DIR__ . '/../../db/content/', '', $filepath);
                    $path = str_replace($filename, '', $path);
                    $fileContents = file_get_contents($filepath);
                    $frontmatter = Frontmatter::fromMarkdownString($fileContents);
                    $contentTypeValue = $frontmatter->getContentType();
                    $contentType = ContentType::fromValueOrDefault($contentTypeValue);

                    if (
                        $contentTypeFilter &&
                        $contentTypeFilter->value &&
                        $contentType->value !== $contentTypeFilter->value
                    ) {
                        continue;
                    }

                    $contentFiles[] = new ContentFile($filename, $path);
                }
            }
        }

        return $contentFiles;
    }

    /**
     * @return Page[]
     */
    public function getContentList(ContentType $contentTypeFilter = null)
    {
        $contentFiles = $this->getContentFilesList($contentTypeFilter);

        $items = [];
        foreach ($contentFiles as $contentFile) {
            $file = file_get_contents(__DIR__ . '/../../db/content/' . $contentFile->getFullPath());
            $contentType = Frontmatter::fromMarkdownString($file)->getContentType();

            if ($contentType === ContentType::PAGE) {
                $item = Page::fromMarkdownString($file);
            } else {
                $item = Post::fromMarkdownString($file);
            }

            $item->setPath($contentFile->getPath() . $contentFile->getFilename());

            $items[] = $item;
        }

        return $items;
    }

    public function getContentItem($filename)
    {
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
