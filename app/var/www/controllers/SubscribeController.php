<?php

namespace app\controllers;

use app\models\Author;
use app\models\Subscriptions;
use Yii;
use yii\db\Exception;
use yii\web\Controller;
use yii\filters\VerbFilter;

class SubscribeController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'subscribe' => ['GET', 'POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * @return string
     * @throws Exception
     */
    public function actionSubscribe(): string
    {
        $authors = Author::find()->all();
        $model = new Subscriptions();

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->save()) {
                Yii::$app->session->setFlash('error', implode("\n", $model->getFirstErrors()));
            }
        }

        return $this->render('subscribe', [
            'model' => $model,
            'authors' => $authors,
        ]);
    }
}
