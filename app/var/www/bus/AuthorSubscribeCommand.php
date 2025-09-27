<?php

namespace app\bus;

readonly class AuthorSubscribeCommand
{
    public function __construct(
        public int $phone,
        public array $authorId,
    )
    {
    }
}