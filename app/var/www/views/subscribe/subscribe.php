<?php

use kartik\select2\Select2;
use yii\bootstrap5\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\SubscribeForm $model */
/** @var app\models\Author[] $authors */

$this->title = 'Подписка на автора';
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="author-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'author_id')->widget(Select2::class, [
        'data' => ArrayHelper::map($authors, 'id', 'name'),
        'options' => [
            'placeholder' => 'Выберите авторов...',
            'multiple' => false,
        ],
        'pluginOptions' => [
            'allowClear' => false,
        ],
    ])->label('Авторы книги') ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
