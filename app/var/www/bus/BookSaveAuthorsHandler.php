<?php

namespace app\bus;

use app\models\BookAuthors;

class BookSaveAuthorsHandler
{
    public function __invoke(BookSaveAuthorsCommand $command): void
    {
        BookAuthors::getDb()->transaction(function () use ($command) {
            BookAuthors::deleteAll(['book_id' => $command->bookId]);

            foreach ($command->authorIds as $authorId) {
                $bookAuthor = new BookAuthors();
                $bookAuthor->book_id = $command->bookId;
                $bookAuthor->author_id = $authorId;
                if (!$bookAuthor->save()) {
                    throw new \RuntimeException('Ошибка сохранения автора: ' . print_r($bookAuthor->errors, true));
                }
            }
        });
    }
}