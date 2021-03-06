<?php
/**
 * @link http://www.xinlvyao.com/
 * @copyright Copyright (c) 2016 四川新绿色药业科技发展股份有限公司
 * @license http://www.xinlvyao.com/
 */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

$this->title = '用户管理';
?>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>用户管理</h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">用户管理</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="box col-md-12">
                <div class="box-header">
                  
                </div>
                <div class="box-body">
                    <div class="box-content"  data-list="<?= Url::toRoute('user/list') ?>" data-del="<?= Url::toRoute('user/del') ?>">
        
                    </div>
                </div>
                
                <?php if(Yii::$app->user->can('user/edit') || Yii::$app->user->identity->username == Yii::$app->params['SuperAdmin']){?>
                    <div class="box-footer clearfix no-border">
                      <button type="button" class="btn btn-default pull-left" data-toggle="modal" data-target="#myDialog" data-data="add"><i class="fa fa-plus"></i> 添加用户</button>
                    </div>
                <?php }?>
        </div>
    </div>
    <div class="modal fade" id="myDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <?php
            $form = ActiveForm::begin([
                        'id' => 'edit-form',
                        'action' => Url::toRoute('user/edit'),
            ]);
            ?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Modal title</h4>
                </div>
                <div class="modal-body">
                    <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
                    <?= $form->field($model, 'username') ?>
                    <?= $form->field($model, 'roles')->checkboxList($listRoles) ?>
                    <?= $form->field($model, 'password')->passwordInput() ?>
                    <?= $form->field($model, 'verifyPassword')->passwordInput()  ?>
                    <?= $form->field($model, 'enabled')->checkbox()->label('启用') ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <?= Html::submitButton('保存', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</section>
<?php
$js = <<<JS
    $(function(){
        getList();
        $('#myDialog').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var data = button.data('data');
            var modal = $(this);
            $("#edit-form")[0].reset();//重置表单
            if(data=='add')
            {
                modal.find('.modal-title').text("添加用户");
                $("#userform-id").val("");
            }
            else{
                modal.find('.modal-title').text("编辑用户");
                $("#userform-id").val(data.id);
                $("#userform-username").val(data.username);
                $("#userform-password").val(data.password);
                $("#userform-verifypassword").val(data.password);
                $("#userform-enabled").prop('checked',data.enabled==1);
                for(var role in data.roles){
                    $("#edit-form input[value='"+role+"']").prop("checked",true);
                }
            }
        });
        $("#edit-form").on('beforeSubmit',function(e){
            ajaxSubmitForm($(this),function(data){
                if(data.status==1){
                    getList();
                    $('#myDialog').modal('hide');
                }
            });
            return false;
        });
   });
   window.getList=function(){
       $.ajax({
        url: $(".box-content").data("list"),
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
                    if (data.status == 1)
                    {
                        layer.alert(data.message, {icon: 6},function(index){
                            getList();
                            layer.close(index);
                        });
                    }
                    else {
                        layer.alert(data.message, {icon: 5}, function (index) {
                            layer.close(index);
                        });
                    }
                }
            });  
        });  
   }
JS;
$this->registerJs($js);
?>