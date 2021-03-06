<?php
/**
 * @link http://www.shuwon.com/
 * @copyright Copyright (c) 2016 成都蜀美网络技术有限公司
 * @license http://www.shuwon.com/
 */
namespace frontend\controllers;
use common\models\Category;
use common\models\News;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\VarDumper;


/**
 * 需控制器。
 * @author 制作人
 * @since 1.0
 */
class NewsController extends CommonController {

    public function beforeAction($action)
    {
        parent::beforeAction($action); // TODO: Change the autogenerated stub
        $this->getSeo(3);
        return true;
    }

	public function actionIndex(){

	    $p_cate = Yii::$app->request->get('p_cate',0);
	    $cate = Yii::$app->request->get('cate',null);
	    if ($p_cate==0){
	        $p_cate = Category::getParent('news')[0]->cate_id;
        }

        if ($cate==0 && !empty(Category::getSon($p_cate))){
            $cate = Category::getSon($p_cate)[0]->cate_id;

        }
        $query = News::find()->where(['enabled'=>1,'news_type'=>'news','news_p_cate'=>$p_cate])->andFilterWhere(['news_cate'=>$cate])->orderBy(['sort'=>SORT_DESC,'news_id'=>SORT_DESC]);

        $data['list'] = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 6,
            ],
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_DESC,
                    'news_id' => SORT_DESC,
                ]
            ],
        ]);
        $data['p_cate'] = $p_cate;
        $data['count'] = $query->count();
        $data['pageSize'] = 6;
        $data['page'] = Yii::$app->request->get('page',1);
        $this->getView()->params['p_cate']=$p_cate;
        $this->getView()->params['cate']=$cate;
        return $this->render('index',$data);
	}

	public function actionDetail(){
        $id = Yii::$app->request->get('id',false);
        if (!$id){
            throw new \yii\web\UnauthorizedHttpException('对不起，您现在还没获此操作的权限');
        }
        News::updateAllCounters(['news_click'=>1],['news_id'=>$id]);
        $info = News::findOne($id);
        $this->getView()->params['p_cate']=$info->news_p_cate;
        $this->getView()->params['cate']=$info->news_cate;
        return $this->render('detail',['info'=>$info]);
    }



}