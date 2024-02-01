<?php

namespace Ainab\Really\Model;

class Frontmatter
{

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

    public function __toString()
    {
        $frontmatter = '---
';

        if ($this->title) {
            $frontmatter = $this->addAttribute($frontmatter, 'title', $this->title);
        }

        if ($this->date) {
            $frontmatter = $this->addAttribute($frontmatter, 'date', $this->date);
        }

        if ($this->slug) {
            $frontmatter = $this->addAttribute($frontmatter, 'slug', $this->slug);
        }

        if (count($this->tags) > 0) {
            $frontmatter = $this->addAttribute($frontmatter, 'tags', $this->tags);
        }

        if (count($this->categories) > 0) {
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

        $frontmatter .= '---

';

        return $frontmatter;
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
        if ($value) {
            $frontmatter .= $attribute . ': ' . $value . '
';
        }
        return $frontmatter;
    }
}
