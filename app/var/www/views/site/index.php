<?php

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

use yii\bootstrap5\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;

$this->title = 'Книги';

?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'id',
        'title',
        'year',
        'description:ntext',
        'isbn',
        'authorsString',
        [
            'class' => ActionColumn::class,
            'template' => '{favorite}',
            'buttons' => [
                'favorite' => function ($url, $model, $key) {
                    return Html::a(
                        'Добавить в избранное',
                        ['subscribe/subscribe'],
                        [
                            'class' => 'btn btn-primary btn-sm',
                        ]
                    );
                }
            ],
        ],
    ],
]); ?>