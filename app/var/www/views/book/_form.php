<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\Book $model */
/** @var app\models\Author[] $authors */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="book-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'year')->textInput([
                'type' => 'number',
                'min' => 1000,
                'max' => (int)date('Y') + 1
            ]) ?>

            <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>
        </div>

        <div class="col-md-4">
            <!-- Поле для загрузки фото -->
            <?= $form->field($model, 'photoFile')->fileInput() ?>

            <!-- Превью фото если есть -->
            <?php if (!$model->isNewRecord && $model->photo): ?>
                <div class="form-group">
                    <label>Текущее фото:</label>
                    <div>
                        <img src="<?= $model->getPhotoUrl() ?>"
                             alt="<?= Html::encode($model->title) ?>"
                             style="max-width: 200px; max-height: 300px;">
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Поле выбора авторов -->
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'authors')->widget(Select2::class, [
                'data' => ArrayHelper::map($authors, 'id', 'name'),
                'options' => [
                    'placeholder' => 'Выберите авторов...',
                    'multiple' => true,
                    'value' => $model->authorIds,
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => false,
                ],
            ])->label('Авторы книги') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>