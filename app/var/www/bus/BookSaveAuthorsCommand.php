<?php

namespace app\bus;

readonly class BookSaveAuthorsCommand
{
    public function __construct(
        public int $bookId,
        public array $authorIds,
    )
    {
    }
}