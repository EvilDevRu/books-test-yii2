<?php

namespace app\controllers;

use app\bus\BookSaveAuthorsCommand;
use app\models\Author;
use app\models\Book;
use League\Tactician\CommandBus;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller
{
    public function __construct(
        $id,
        $module,
        protected CommandBus $commandBus,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                //  todo: access
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Book models.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Book::find()->with('authorsRelation'),
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Book model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Book model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     */
    public function actionCreate(): Response|string
    {
        $model = new Book();
        $authors = Author::find()->all();

        if ($this->request->isPost) {
            $model->load($this->request->post());
            $model->photoFile = UploadedFile::getInstance($model, 'photoFile');

            try {
                if (!$model->validate()) {
                    throw new \RuntimeException(implode("\n", $model->getFirstErrors()));
                }

                Book::getDb()->transaction(function() use ($model) {
                    $model->upload();
                    $model->save();

                    $this->commandBus->handle(new BookSaveAuthorsCommand($model->id, $model->authors));
                });

                return $this->redirect(['view', 'id' => $model->id]);
            } catch (\Throwable $e) {
                \Yii::$app->session->addFlash('error', $e->getMessage());
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
            'authors' => $authors,
        ]);
    }

    /**
     * Updates an existing Book model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id): Response|string
    {
        $model = $this->findModel($id);
        $authors = Author::find()->all();

        if ($this->request->isPost) {
            $model->load($this->request->post());
            $model->photoFile = UploadedFile::getInstance($model, 'photoFile');

            try {
                if (!$model->validate()) {
                    throw new \RuntimeException(implode("\n", $model->getFirstErrors()));
                }

                Book::getDb()->transaction(function() use ($model) {
                    $model->upload();
                    $model->save();

                    $this->commandBus->handle(new BookSaveAuthorsCommand($model->id, $model->authors));
                });

                return $this->redirect(['view', 'id' => $model->id]);
            } catch (\Throwable $e) {
                \Yii::$app->session->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('update', [
            'model' => $model,
            'authors' => $authors,
        ]);
    }

    /**
     * Deletes an existing Book model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): Book
    {
        if (($model = Book::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
