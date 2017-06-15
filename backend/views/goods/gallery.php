<?php
use xj\uploadify\Uploadify;
use yii\bootstrap\Html;
use yii\web\JsExpression;

echo Html::fileInput('test', NULL, ['id' => 'test']);
echo Uploadify::widget([
    'url' => yii\helpers\Url::to(['s-upload']),
    'id' => 'test',
    'csrf' => true,
    'renderTag' => false,
    'jsOptions' => [
        'formData'=>['goods_id'=>$goods->id],//上传文件的同时传参goods_id
        'width' => 120,
        'height' => 40,
        'onUploadError' => new JsExpression(<<<EOF
function(file, errorCode, errorMsg, errorString) {
    console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
}
EOF
        ),
        'onUploadSuccess' => new JsExpression(<<<EOF
function(file, data, response) {
    data = JSON.parse(data);
    if (data.error) {
        console.log(data.msg);
    } else {
        console.log(data);
        //以下为拼接要显示的Html
        var html='<tr data-id="'+data.goods_id+'" id="gallery_'+data.goods_id+'">';
        html += '<td><img src="'+data.fileUrl+'" /></td>';
        html += '<td><button type="button" class="btn btn-danger del_btn">删除</button></td>';
        html += '</tr>';
        $("table").append(html);
    }
}
EOF
        ),
    ]
]);

?>
    <table class="table">
        <tr>
            <th>图片</th>
            <th>操作</th>
        </tr>
        <?php foreach($goods->galleries as $gallery):?>
            <tr id="gallery_<?=$gallery->id?>" data-id="<?=$gallery->id?>">
                <td><?=Html::img($gallery->logo)?></td>
                <td><?=Html::button('删除',['class'=>'btn btn-danger del_btn'])?></td>
            </tr>
        <?php endforeach;?>
    </table>
<?php
$url = \yii\helpers\Url::to(['del-gallery']);
$this->registerJs(new JsExpression(
    <<<EOT
    $("table").on('click',".del_btn",function(){
        if(confirm("确定删除该图片吗?")){
        var id = $(this).closest("tr").attr("data-id");
            $.post("{$url}",{id:id},function(data){
                if(data=="success"){
                    //alert("删除成功");
                    $("#gallery_"+id).remove();
                }
            });
        }
    });
EOT

));