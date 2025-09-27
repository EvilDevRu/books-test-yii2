<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class SubscribeForm extends Model
{
    public $phone;
    public $authorId;


    /**
     * @return array the validation rules.
     */
    public function rules(): array
    {
        return [
            [['phone', 'authorId'], 'required'],
            ['authorId', 'exist', 'targetClass' => Author::class, 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels(): array
    {
        return [
            //  todo
        ];
    }
}
