<?php
/**
 * @link http://www.shuwon.com/
 * @copyright Copyright (c) 2016 成都蜀美网络技术有限公司
 * @license http://www.shuwon.com/
 */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;

$cate = ArrayHelper::map(\common\models\Category::getParent('news'),'cate_id','cate_title');
$type = Yii::$app->request->get('type');
$span = $cate[$type];
$this->title = '后台首页';
$this->params['breadcrumbs'][] = $span.'列表';
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1><?=$span?>列表<a href="<?= Url::toRoute(['news/edit','type'=>$type]) ?>" class="btn btn-primary" style="margin-left: 20px;"><i class="glyphicon glyphicon-plus-sign"></i><span>添加</span></a></h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active"><?=$span?>列表</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
<div class="row">
    <div class="box col-md-12">
        <div class="box-inner">
            <div class="box-header" data-original-title="">
                
            </div>

            <div class="box-create">
                    <?php
	                $form = ActiveForm::begin([
	                     'id' => 'list-form',
                         'action' => Url::toRoute('news/list'),
                         'options' => ['class' => 'form-horizontal'],
	                ]);
	                ?>
	                 	                  				         
			        <div class="form-group form-group-sm">
				        <label class="control-label col-md-1">标题</label><div class="col-md-2"><input class="form-control" type="text" name="news_title"></div>
                        <label class="control-label col-md-1">推荐</label><div class="col-md-2"><?= Html::dropDownList("recommend", 0, Yii::$app->params["recommend"],["prompt" => "全部", "class" => "form-control"]) ?></div>
                    </div>
				    
				    <div class="form-group form-group-sm">

				        <label class="control-label col-md-1">审核</label><div class="col-md-2"><?= Html::dropDownList("enabled", 0, Yii::$app->params["enabled"],["prompt" => "全部", "class" => "form-control"]) ?></div>			             			             
	                    <input name="page" value="1" type="hidden">
	                    <input name="type" value="<?=$type?>" type="hidden">
	                    <div class="col-md-2">
	                        <a href="#" onclick="getList(1)" class="btn btn-default btn-block" ><i class="glyphicon glyphicon-search"></i><span>查询</span></a>
	                    </div>
	                </div>

	                <?php ActiveForm::end(); ?>
            </div>
            
            <div class="box-content"  data-list="<?= Url::toRoute('news/list') ?>" data-del="<?= Url::toRoute('news/del') ?>">

            </div>
            
        </div>
    </div>
</div>
</section>
<?php
$js=<<<JS
   $(function(){
        getList();
   });
   //获得新列表
   window.getList=function(page){
 	   page!=null?$("#list-form input[name='page']").val(page):null; 
       $.ajax({
        url: $(".box-content").data("list"),
 		data:$("#list-form").serialize(),
        beforeSend: function () {
            layer.load();
        },
        complete: function () {
            layer.closeAll('loading');
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            layer.alert('出错拉:' + textStatus + ' ' + errorThrown, {icon: 5});
        },
        success: function (data) {
            $(".box-content").html(data);
        }
    });
   }
   //禁用新信息
   window.del=function(id){
        layer.confirm('确定删除?', function(index){
            layer.close(index);
            $.ajax({
                url: $(".box-content").data("del"),
                data:{
                    id:id
                },
                beforeSend: function () {
                    layer.load();
                },
                complete: function () {
                    layer.closeAll('loading');
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    layer.alert('出错拉:' + textStatus + ' ' + errorThrown, {icon: 5});
                },
                success: function (data) {
                    if (data.status == 1){
                        layer.alert(data.message, {icon: 6},function(index){
                            getList();
                            layer.close(index);
                        });
                    }else{
                        layer.alert(data.message, {icon: 5}, function (index) {
                            layer.close(index);
                        });
                    }
                }
            });  
        });  
   }
   window.goPage=function(obj){
          var page=$(obj).data('page')+1;
          getList(page);
          return false;
   }
JS;
$this->registerJs($js);
?>
