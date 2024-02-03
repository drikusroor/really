<?php

namespace Ainab\Really\Model;

use Ainab\Really\Model\Page;

class PageCollection
{
    /**
     * @param Page[] $pages
     */
    public function __construct(private array $pages = [])
    {
    }

    /**
     * @return Page[]
     */
    public function toArray(): array
    {
        $collection = [];

        foreach ($this->pages as $page) {
            $collection[] = $page->toArray();
        }

        return $collection;
    }
}
