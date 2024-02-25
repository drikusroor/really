<?php

namespace Ainab\Really\Model;

class ContentInput
{
    public function __construct(
        private string $contentType,
        private string $title,
        private string $slug,
        private string $content,
        private string $date,
        private array $tags = [],
        private array $categories = [],
        private bool $draft = false,
        private string $layout = 'page',
        private string $author = '',
        private string $excerpt = '',
        private string $filepath = ''

    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['contentType'],
            $data['title'],
            $data['slug'],
            $data['content'],
            $data['date'],
            explode(',', $data['tags']),
            explode(',', $data['categories']),
            isset($data['draft']),
            $data['layout'],
            $data['author'],
            $data['excerpt'],
            $data['filepath'],
        );
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getDraft(): bool
    {
        return $this->draft;
    }

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getExcerpt(): string
    {
        return $this->excerpt;
    }

    public function getFilepath(): string
    {
        return $this->filepath;
    }
}
