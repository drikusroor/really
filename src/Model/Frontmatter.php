<?php

namespace Ainab\Really\Model;

class Frontmatter
{
    private $contentType = ContentType::POST;
    private $title;
    private $date;
    private $slug;
    private $tags;
    private $categories;
    private $draft;
    private $layout;
    private $author;
    private $excerpt;

    public function __construct(
        string $contentType,
        string $title,
        $date = null,
        ?string $slug = null,
        $tags = [],
        $categories = [],
        ?bool $draft = false,
        ?string $layout = 'page',
        ?string $author = null,
        ?string $excerpt = null
    ) {
        $this->contentType = $contentType;
        $this->title = $title;
        if (!$date) {
            $date = \date('Y-m-d');
        }
        $this->date = $date;
        $this->slug = $slug;
        $this->tags = $tags;
        $this->categories = $categories;
        $this->draft = $draft;
        $this->layout = $layout;
        $this->author = $author;
        $this->excerpt = $excerpt;
    }


    public static function fromMarkdownString(string $markdown): Frontmatter
    {
        $frontmatter = substr($markdown, 0, strpos($markdown, '---', 3) + 3);
        return Frontmatter::fromString($frontmatter);
    }

    public static function fromString(string $frontmatter): Frontmatter
    {
        $frontmatter = str_replace('---', '', $frontmatter);
        $attributes = explode("\n", $frontmatter);
        $attributes = array_filter($attributes, function ($attribute) {
            return $attribute !== '';
        });

        $contentType = ContentType::POST;
        $title = null;
        $date = null;
        $slug = null;
        $tags = [];
        $categories = [];
        $draft = false;
        $layout = 'page';
        $author = null;
        $excerpt = null;

        foreach ($attributes as $attribute) {
            $attribute = explode(':', $attribute);
            $attribute[0] = trim($attribute[0]);
            $attribute[1] = trim($attribute[1]);

            switch ($attribute[0]) {
                case 'contentType':
                    $contentTypeValue = $attribute[1];
                    $contentType = ContentType::fromValueOrDefault($contentTypeValue);
                    break;
                case 'title':
                    $title = $attribute[1];
                    break;
                case 'date':
                    $date = $attribute[1];
                    break;
                case 'slug':
                    $slug = $attribute[1];
                    break;
                case 'tags':
                    $tags = explode(',', $attribute[1]);
                    $tags = array_map('trim', $tags);
                    break;
                case 'categories':
                    $categories = explode(',', $attribute[1]);
                    $categories = array_map('trim', $categories);
                    break;
                case 'draft':
                    $draft = $attribute[1] === 'true' ? true : false;
                    break;
                case 'layout':
                    $layout = $attribute[1];
                    break;
                case 'author':
                    $author = $attribute[1];
                    break;
                case 'excerpt':
                    $excerpt = $attribute[1];
                    break;
            }
        }

        return new Frontmatter(
            $contentType->value,
            $title,
            $date,
            $slug,
            $tags,
            $categories,
            $draft,
            $layout,
            $author,
            $excerpt
        );
    }

    public function __toString()
    {
        $frontmatter = '---
';

        $frontmatter = $this->addAttribute($frontmatter, 'contentType', $this->contentType);

        if ($this->title) {
            $frontmatter = $this->addAttribute($frontmatter, 'title', $this->title);
        }

        if ($this->date) {
            $frontmatter = $this->addAttribute($frontmatter, 'date', $this->date);
        }

        if ($this->slug) {
            $frontmatter = $this->addAttribute($frontmatter, 'slug', $this->slug);
        }

        if ($this->tags && count($this->tags) > 0) {
            $frontmatter = $this->addAttribute($frontmatter, 'tags', $this->tags);
        }

        if ($this->tags && count($this->categories) > 0) {
            $frontmatter = $this->addAttribute($frontmatter, 'categories', $this->categories);
        }

        if ($this->draft) {
            $frontmatter = $this->addAttribute($frontmatter, 'draft', $this->draft);
        }

        if ($this->layout) {
            $frontmatter = $this->addAttribute($frontmatter, 'layout', $this->layout);
        }

        if ($this->author) {
            $frontmatter = $this->addAttribute($frontmatter, 'author', $this->author);
        }

        if ($this->excerpt) {
            $frontmatter = $this->addAttribute($frontmatter, 'excerpt', $this->excerpt);
        }

        if ($this->contentType) {
            $contentType = ContentType::fromValueOrDefault($this->contentType);
            $frontmatter = $this->addAttribute($frontmatter, 'contentType', $contentType->value);
        }

        $frontmatter .= '---

';

        return $frontmatter;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function getDraft()
    {
        return $this->draft;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getExcerpt()
    {
        return $this->excerpt;
    }

    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    public function setDraft($draft)
    {
        $this->draft = $draft;
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function setExcerpt($excerpt)
    {
        $this->excerpt = $excerpt;
    }

    private function addAttribute($frontmatter, $attribute, $value)
    {

        if (is_array($value)) {
            $value = implode(', ', $value);
            // trim any leading or trailing commas
            $value = trim($value, ',');
            // remove any double spaces
            $value = preg_replace('/\s+/', ' ', $value);
        }

        if ($value) {
            $frontmatter .= $attribute . ': ' . $value . '
';
        }
        return $frontmatter;
    }
}
