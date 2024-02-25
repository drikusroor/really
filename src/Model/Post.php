<?php

namespace Ainab\Really\Model;

use Ainab\Really\Model\Frontmatter;

class Post
{
    public function __construct(private Frontmatter $frontmatter, private string $content, private string $path = '')
    {
    }

    public static function fromMarkdownString(string $markdown): Post
    {
        $frontmatter = Frontmatter::fromMarkdownString($markdown);
        $content = Post::getContentFromMarkdown($markdown);

        // remove whitelines from the beginning of the content and the end
        $content = trim($content);

        return new Post($frontmatter, $content);
    }

    public static function getContentFromMarkdown(string $markdown): string
    {
        $content = $markdown;
        if (strpos($markdown, '---') === 0) {
            $content = substr($markdown, strpos($markdown, '---', 3) + 3);
        }
        return $content;
    }

    public function getFrontmatter(): Frontmatter
    {
        return $this->frontmatter;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getSlug(): string
    {
        return $this->frontmatter->getSlug() ?? '';
    }

    public function getTitle(): string
    {
        return $this->frontmatter->getTitle() ?? '';
    }

    public function getDate(): string
    {
        return $this->frontmatter->getDate() ?? '';
    }

    public function getTags(): array
    {
        return $this->frontmatter->getTags() ?? [];
    }

    public function getCategories(): array
    {
        return $this->frontmatter->getCategories() ?? [];
    }

    public function getDraft(): bool
    {
        return $this->frontmatter->getDraft() ?? false;
    }

    public function getLayout(): string
    {
        return $this->frontmatter->getLayout() ?? 'post';
    }

    public function getAuthor(): string
    {
        return $this->frontmatter->getAuthor() ?? '';
    }

    public function getExcerpt(): string
    {
        return $this->frontmatter->getExcerpt() ?? '';
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function __toString(): string
    {
        return $this->frontmatter->getTitle();
    }

    public function toArray(): array
    {
        return [
            'title' => $this->getTitle(),
            'date' => $this->getDate(),
            'content' => $this->getContent(),
            'slug' => $this->getSlug(),
            'tags' => $this->getTags(),
            'categories' => $this->getCategories(),
            'draft' => $this->getDraft(),
            'layout' => $this->getLayout(),
            'author' => $this->getAuthor(),
            'excerpt' => $this->getExcerpt(),
            'path' => $this->getPath(),
        ];
    }
}
