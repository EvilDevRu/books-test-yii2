<?php

namespace app\models;

use Yii;
use yii\base\Model;

class TopAuthorsForm extends Model
{
    public $year;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        $currentYear = (int)date('Y');

        return [
            [['year'], 'required'],
            [['year'], 'integer', 'min' => 1000, 'max' => $currentYear + 1],
            [['year'], 'filter', 'filter' => 'intval'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'year' => 'Год',
        ];
    }

    /**
     * Получает доступные годы из базы данных
     */
    public static function getAvailableYears(): array
    {
        return Book::find()
            ->select('year')
            ->orderBy(['year' => SORT_DESC])
            ->column();
    }
}