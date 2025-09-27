<?php

/** @var yii\web\View $this */
/** @var app\models\TopAuthorsForm $model */
/** @var Author[] $topAuthors */
/** @var int[] $availableYears */
/** @var int $selectedYear */

use app\models\Author;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;

$this->title = 'Топ 10 авторов за ' . $selectedYear . ' год';
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

?>

<div class="site-top">
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- Форма выбора года с ActiveForm -->
    <div class="row mb-4">
        <div class="col-md-6">
            <?php $form = ActiveForm::begin([
                'method' => 'get',
                'action' => ['site/top'],
                'options' => ['class' => 'form-inline'],
                'fieldConfig' => [
                    'options' => ['class' => 'form-group mr-2'],
                    'labelOptions' => ['class' => 'mr-2'],
                ],
            ]); ?>

            <?= $form->field($model, 'year')->dropDownList(
                array_combine($availableYears, $availableYears),
                [
                    'class' => 'form-control',
                    'prompt' => 'Выберите год...',
                    'value' => $selectedYear,
                ]
            )->label('Год') ?>

            <?= Html::submitButton('Показать', ['class' => 'btn btn-primary mr-2']) ?>
            <?= Html::a('Сбросить', ['top'], ['class' => 'btn btn-secondary']) ?>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <!-- Таблица с результатами -->
    <?php if (!empty($topAuthors)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Автор</th>
                    <th>Количество книг</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($topAuthors as $index => $author): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <?= Html::encode($author['name']) ?>
                        </td>
                        <td>
                            <?= $author['books_count'] ?>
                        </td>
                        <td>
                            <?= Html::a(
                                'Профиль автора',
                                ['/author/view', 'id' => $author['id']],
                                ['class' => 'btn btn-sm btn-outline-secondary']
                            ) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <h4>Нет данных</h4>
            <p>За <?= $selectedYear ?> год не найдено книг или авторов.</p>
            <p>Попробуйте выбрать другой год из списка выше.</p>
        </div>
    <?php endif; ?>
</div>