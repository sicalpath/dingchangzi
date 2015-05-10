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

$(function(){

	var layerContent = '' ;
		layerContent += '<div class="dcz-form">'
					+ '<div class="form-group">'
					+ '	<input type="text" id="phone" class="form-control" placeholder="手机号" />'
					+ '</div>'
					+ '<div class="form-group">'
					+'	<input type="text" id="verify" class="form-control" placeholder="六位取号密码" />'
					+'</div>'
					+'<div class="form-group">'
					+'	<input type="button" class="btn-center dcz-btn btn-default btn-lg btn-inline" onclick="verify();" value="确定">'
					+'</div>'
					+'</div>' ;
	var options = {
		layerTitle:"订单验证",
		layerContent:layerContent
	}

	$('#orderVerify').on('click',function(){
		// console.log(1);
		layer(options);
	});

})