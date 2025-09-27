<?php

namespace app\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "books".
 *
 * @property int $id
 * @property string $title
 * @property int $year
 * @property string|null $description
 * @property string $isbn
 * @property string $photo
 * @property string|null $created_at
 * @property string|null $updated_at
 *
 * @property UploadedFile|null $photoFile
 * @property array $authorIds
 *
 * @property-read BookAuthors[] $bookAuthorsRelation
 * @property-read Author[] $authorsRelation
 */
class Book extends ActiveRecord
{
    public $photoFile;
    public $authors = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%books}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title', 'year', 'isbn'], 'required'],
            [['year'], 'integer', 'min' => 1000, 'max' => (int)date('Y') + 1],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['isbn'], 'string', 'max' => 20],
            [['isbn'], 'match', 'pattern' => '/^[0-9\-]+$/', 'message' => 'ISBN может содержать только цифры и дефисы'],
            [['photo'], 'string', 'max' => 500],
            [['photo'], 'default', 'value' => null],
            [['isbn'], 'unique'],
            [['authors'], 'each', 'rule' => ['integer']],
            [['authors'], 'default', 'value' => []],
            [['title', 'year', 'isbn', 'description'], 'filter', 'filter' => 'trim'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'title' => 'Название книги',
            'year' => 'Год издания',
            'description' => 'Описание',
            'isbn' => 'ISBN',
            'photo' => 'Фото обложки',
            'photoFile' => 'Файл обложки',
            'authorIds' => 'Авторы',
            'authorsString' => 'Авторы',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * Загрузка и сохранение файла фото
     * @return bool
     * @throws Exception
     */
    public function upload(): bool
    {
        if ($this->photoFile) {
            $fileName = Yii::$app->security->generateRandomString(12) . '.' . $this->photoFile->extension;
            $filePath = Yii::getAlias('@webroot/uploads/books/') . $fileName;

            // Создаем директорию если не существует
            $dir = dirname($filePath);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            if ($this->photoFile->saveAs($filePath)) {
                // Удаляем старое фото если оно есть
                if ($this->photo && file_exists(Yii::getAlias('@webroot') . $this->photo)) {
                    unlink(Yii::getAlias('@webroot') . $this->photo);
                }

                $this->photo = '/uploads/books/' . $fileName;
                return true;
            }
        }

        return false;
    }

    /**
     * Gets query for [[BookAuthors]].
     *
     * @return ActiveQuery
     */
    public function getBookAuthorsRelation(): ActiveQuery
    {
        return $this->hasMany(BookAuthors::class, ['book_id' => 'id']);
    }

    /**
     * Gets query for [[Authors]].
     *
     * @return ActiveQuery
     */
    public function getAuthorsRelation(): ActiveQuery
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->via('bookAuthorsRelation');
    }

    public function getAuthorIds(): array
    {
        return ArrayHelper::getColumn($this->authorsRelation, 'id');
    }

    /**
     * Получает список авторов в виде строки
     * @return string
     */
    public function getAuthorsString(): string
    {
        return implode(', ', ArrayHelper::getColumn($this->authorsRelation, 'name'));
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete(): void
    {
        parent::afterDelete();

        // Удаляем файл фото при удалении книги
        if ($this->photo && file_exists(Yii::getAlias('@webroot') . $this->photo)) {
            unlink(Yii::getAlias('@webroot') . $this->photo);
        }
    }

    /**
     * Получает URL фото обложки
     * @return string
     */
    public function getPhotoUrl(): string
    {
        return Yii::$app->request->baseUrl . $this->photo;
    }

    /**
     * Получает абсолютный путь к фото
     * @return string
     */
    public function getPhotoPath(): ?string
    {
        if ($this->photo) {
            return Yii::getAlias('@webroot') . $this->photo;
        }
        return null;
    }
}