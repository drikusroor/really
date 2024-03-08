<?php

namespace Ainab\Really\Model;

class ContentFile
{
    public function __construct(
        private string $filename,
        private string $path,
    ) {
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFullPath(): string
    {
        return $this->path . '/' . $this->filename;
    }
}
