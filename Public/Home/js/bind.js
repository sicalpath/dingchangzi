var layer = function (options){
	var layerTitle,layerContent;
    if (!options) {
    	layerTitle = "" ;
    	layerContent = "" ;
    }
    layerTitle = options.layerTitle ;
    layerContent = options.layerContent ;
    var html = "" ;
    html += '<div class="layer-mask">'
    	 + '	<div class="layer-container">'
    	 + '		<div class="layer-header">'
    	 +	'			<h1 class="layer-title">'+layerTitle+'</h1>'
    	 + '			<i class="dcz-icon icon-close layer-close"></i>'
    	 + '		</div>'
    	 +	'		<div class="layer-body">'
    	 + layerContent
    	 +	'		</div>'
    	 + '	</div>'
    	 + '</div>' ;
    $('body').append(html) ;
    $('.layer-close').on('click',function(){
    	$('.layer-mask').remove() ;
    }) ;
}

function player(){

    var layerContent = '' ;
        layerContent += '<div class="dcz-form">'
                    + '<div class="form-group">'
                    + ' <input type="text" id="phone" class="form-control" placeholder="手机号" />'
                    + '</div>'
                    + '<div class="form-group">'
                    +'<input type="button" class="btn-center dcz-btn btn-default btn-lg btn-inline btn-send" value="获取验证码" onclick="sendP();"/>'
                    +'<input type="text" class="form-control input-addon right" id="verify" placeholder="输入验证码" style="width:120px;" name="code">'
                    +'</div>'
                    +'<div class="form-group">'
                    +'  <input type="button" class="btn-center dcz-btn btn-default btn-lg btn-inline" onclick="verify();" value="确定">'
                    +'</div>'
                    +'</div>' ;
    var options = {
        layerTitle:"绑定手机",
        layerContent:layerContent
    }
    layer(options);

}

function verify(){
     var url = "http://www.dingchangzi.net/index.php/Api/bind.html";

    data = {phone:$('#phone').val(),verify:$('#verify').val(),action:"0"};
    
    $.post(url,data,function(res){
        if(res == "true")
            alert("绑定成功");
        else
            alert("绑定失败");
    });
}

function sendP(){
    var url = "http://www.dingchangzi.net/index.php/Login/send.html";
    $.post(url,{to:$("#phone").val()},function(data,textStatus){alert(data);});
}
