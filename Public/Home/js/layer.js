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
					+ '	<input type="text" id="phone" class="form-control layer-input" placeholder="绑定手机号" />'
                    + ' <span class="dcz-btn btn-primary btn-sm btn-inline btn-right">发送验证码</span>'
					+ '</div>'
					+ '<div class="form-group">'
					+'	<input type="text" id="verify" class="form-control layer-input" placeholder="输入验证码" />'
					+'</div>'
					+'<div class="form-group">'
					+'	<input type="button" class="btn-right dcz-btn btn-primary btn-sm btn-inline" onclick="verify();" value="确定">'
					+'</div>'
					+'</div>' ;
	var options = {
		layerTitle:"手机号绑定",
		layerContent:layerContent
	}

	$('#bindPhone').on('click',function(){
		// console.log(1);
		layer(options);
	});

})


$(function(){

    var layerContent = '' ;
        layerContent += '<div class="dcz-form">'
                    + '<div class="form-group  area-group">'
                    + '  <div class="filter-select" id="citySelectArea"></div> <div class="filter-select" id="schoolSelectArea"></div> '
                    +'</div>'
                    +'<div class="form-group">'
                    +'  <input type="button" class="btn-right dcz-btn btn-primary btn-sm btn-inline" onclick="verify();" value="确定">'
                    +'</div>'
                    +'</div>' //;
                    +'<script  src="http://www.dingchangzi.net//Public/Home/js/layerDownList.js"></script>' ;
    var options = {
        layerTitle:"学号绑定",
        layerContent:layerContent
    }

    $('#bindNumber').on('click',function(){
        // console.log(1);
        layer(options);
    });

})