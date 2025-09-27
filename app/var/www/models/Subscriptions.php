<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%subscriptions}}".
 *
 * @property int $id
 * @property int $author_id
 * @property string $phone
 *
 * @property Author $authorRelation
 */
class Subscriptions extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%subscriptions}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['author_id', 'phone'], 'required'],
            [['author_id'], 'integer'],
            [['phone'], function ($attribute, $params) {
                $phone = $this->$attribute;

                // Очищаем номер
                $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);

                // Базовые проверки
                if (empty($cleanPhone)) {
                    $this->addError($attribute, 'Телефон не может быть пустым');
                    return;
                }

                // Должен начинаться с + или с цифры
                if (!preg_match('/^(\+|0-9)/', $cleanPhone)) {
                    $this->addError($attribute, 'Телефон должен начинаться с + или цифры');
                    return;
                }

                // Минимальная и максимальная длина
                $digitCount = strlen(preg_replace('/[^0-9]/', '', $cleanPhone));
                if ($digitCount != 11) {
                    $this->addError($attribute, 'Номер должен содержать 11 цифр');
                    return;
                }

                // Проверка по E.164 стандарту (международный формат)
                if (!preg_match('/^\+[1-9]\d{1,14}$/', $cleanPhone)) {
                    // Если нет + в начале, добавляем
                    if (!str_starts_with($cleanPhone, '+')) {
                        $cleanPhone = '+' . ltrim($cleanPhone, '0');
                    }

                    // Проверяем еще раз
                    if (!preg_match('/^\+[1-9]\d{1,14}$/', $cleanPhone)) {
                        $this->addError($attribute, 'Неверный международный формат телефона');
                        return;
                    }
                }

                $this->$attribute = $cleanPhone;
            }],
            [['author_id', 'phone'], 'unique', 'targetAttribute' => ['author_id', 'phone']],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => Author::class, 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'author_id' => 'Author ID',
            'phone' => 'Phone',
        ];
    }

    /**
     * Gets query for [[Author]].
     *
     * @return ActiveQuery
     */
    public function getAuthorRelation(): ActiveQuery
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }

}
