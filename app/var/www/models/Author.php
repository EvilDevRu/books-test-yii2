<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "authors".
 *
 * @property int $id
 * @property string $name
 * @property int|null $birth_year
 * @property string|null $biography
 *
 * @property BookAuthors[] $bookAuthors
 * @property Book[] $books
 */
class Author extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%authors}}';
    }

    /**
     * {@inheritdoc}
     */
    public static function find(): AuthorQuery
    {
        return new AuthorQuery(static::class);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Имя автора',
            'birth_year' => 'Год рождения',
            'biography' => 'Биография',
        ];
    }

    /**
     * Gets query for [[BookAuthors]].
     *
     * @return ActiveQuery
     */
    public function getBookAuthorsRelation(): ActiveQuery
    {
        return $this->hasMany(BookAuthors::class, ['author_id' => 'id']);
    }

    /**
     * Gets query for [[Books]].
     *
     * @return ActiveQuery
     */
    public function getBooksRelation(): ActiveQuery
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])
            ->via('bookAuthors');
    }

    /**
     * Получает список авторов для dropdown
     * @return array
     */
    public static function getList(): array
    {
        return static::find()
            ->select(['name'])
            ->indexBy('id')
            ->orderBy('name')
            ->column();
    }

    /**
     * Получает строку с книгами автора
     * @return string
     */
    public function getBooksString(): string
    {
        $books = [];
        foreach ($this->books as $book) {
            $books[] = $book->title;
        }
        return implode(', ', $books);
    }
}