<?php

namespace Ainab\Really\Model;

use Ainab\Really\Model\Frontmatter;

class Page {
    public function __construct(private Frontmatter $frontmatter, private string $content) {
    }

    public static function fromMarkdownString(string $markdown): Page {
        $frontmatter = Frontmatter::fromMarkdownString($markdown);
        $content = Page::getContentFromMarkdown($markdown);

        return new Page($frontmatter, $content);
    }

    public static function getContentFromMarkdown(string $markdown): string {
        $content = $markdown;
        if (strpos($markdown, '---') === 0) {
            $content = substr($markdown, strpos($markdown, '---', 3) + 3);
        }
        return $content;
    }

    public function getFrontmatter(): Frontmatter {
        return $this->frontmatter;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function getSlug(): string {
        return $this->frontmatter->getSlug();
    }

    public function getTitle(): string {
        return $this->frontmatter->getTitle();
    }
}