<?php

namespace Ainab\Really\Model;

enum ContentType: string
{
    case PAGE = 'page';
    case POST = 'post';

    public static function fromValueOrDefault(string $value): self
    {
        $type = self::tryFrom($value);

        return $type ?? self::POST;
    }
}
