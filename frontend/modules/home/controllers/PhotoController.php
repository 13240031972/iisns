<?php

namespace app\modules\home\controllers;

use Yii;
use common\components\BaseController;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\modules\home\models\Photo;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class PhotoController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
    	$this->layout = '@app/modules/user/views/layouts/user';
        return $this->render('index');
    }

    public function actionView($id)
    {
        $this->layout = '@app/modules/user/views/layouts/user';
        $model = $this->findModel($id);
        if ($model->created_by !== Yii::$app->user->id) {
            throw new ForbiddenHttpException('You are not allowed to perform this action.');
        }

        return $this->render('view', [
            'model' => $model
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->Request->isAjax && $model->created_by === Yii::$app->user->id) {
            $albumId = $model->album_id;
            $model->delete();
            Yii::setAlias('@photo_path', '@webroot/uploads/home/photo/');
            @unlink(Yii::getAlias('@photo_path').$model->path); 
            return true;
        } else {
            throw new ForbiddenHttpException('You are not allowed to perform this action.');
        }
    }

    /**
     * Finds the Photo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Photo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Photo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
