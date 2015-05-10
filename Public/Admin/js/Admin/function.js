function doSyn(){
    setTimeout("syncStatus()",Math.random()*5000);
}
//获取实时信息
function syncStatus(){
    var url = 'http://www.dingchangzi.net/buser.php/Api/getSyncStatus';

    $.getJSON(url,function(data){
        if( data != null){
            $(data).each(function(index){
                messageAlert(data[index]);
                //alert(data[index]['date']+"的"+  + "已被预订");
            })
        }
    })
}
//弹窗
function messageAlert(data){
    //设置消息条通知内容
        time = (parseInt(data['ftime'])+8) + ":00-"+(parseInt(data['ftime'])+9) +":00"
        var content = '日期:'+data['date']+'的,'+data['fid']+'号场地,'+time+' 已被预订.';

        new PNotify({
            title: '预订提示',
            text: content,
            closer: true, 
            shadow: true,
            opacity: 0.9,
            hide: true
        });
}
//验证订单
function verify(){
     var url = "http://www.dingchangzi.net/buser.php/Api/verify.html";

    data = {phone:$('#phone').val(),verify:$('#verify').val(),stid:1};
    
    $.post(url,data,function(res){
        if(res == "true")
            alert("验证成功");
        else
            alert("验证失败");
    });
}
function getStatus(fid,ftime){
    //var json = $.getJSON("{:U('getStatus')}");
    return $.getJSON('http://www.dingchangzi.net/buser.php/Api/getStatus?fid='+fid+'&ftime='+ftime);
}

function setStatus(obj,status,price){
    var color =new Array("dcz-icon icon-common-selled","dcz-icon icon-common-avaliable availiable","dcz-icon icon-common-selected","dcz-icon icon-common-vip","dcz-icon icon-common-locked");
    //0不可用 1可预订 2已订 3仅学生

    if(status == 1){
        var html = "￥" + price ;
        obj.innerHTML = html;
        obj.setAttribute('class',color[status]);
        obj.setAttribute('price',price);
        obj.setAttribute('style','font-style:normal;line-height:34px;');
    }
    else
        obj.setAttribute('class',color[status]);
}

//遍历设置场地状态(按每时段)
function iterates(){
    var fields = $('.field');
    //var status = 
    for (var i = fields.length - 1; i >= 0; i--) {
        var ftimes = $('.field:eq('+ i +')').siblings().find('i');
        fid = $('.field:eq('+ i +')').attr('fid');
        for (var j = 0; j < ftimes.length; j++) {
            obj = ftimes[j];
            ftime = obj.getAttribute('ftime');
            obj.setAttribute('id',fid+ftime)
            //console.log(obj,ftime)
            $.ajaxSettings.async = false;       //设置同步加载
            $.getJSON('http://www.dingchangzi.net/buser.php/Api/getStatus?fid='+fid+'&ftime='+ftime,function(data){
                setStatus(obj,data.status,data.price);
            });

        };
        
    };

}
//遍历设置场地状态(按场地)
function iterates2(date){
    var fields = $('.field');
    //var status = 
    for (var i = fields.length - 1; i >= 0; i--) {
        var ftimes = $('.field:eq('+ i +')').siblings().find('i');
        fid = $('.field:eq('+ i +')').attr('fid');
        $.ajaxSettings.async = false;       //设置同步加载
        $.getJSON('http://www.dingchangzi.net/buser.php/Api/getStatus2?fid='+fid+'&date='+date,function(data){
            for (var j = 0; j < ftimes.length; j++) {
                obj = ftimes[j];
                ftime = obj.getAttribute('ftime');
                obj.setAttribute('id',fid+ftime)
                setStatus(obj,data[j].status,data[j].price);
            }

        });
        
    }
}
function addToCart(fid,ftime,price,sort){
    date = $('#date').val().split("/")[1] + "/" + $('#date').val().split("/")[2];
    time = (parseInt(ftime)%14+8) + ":00-" + (parseInt(ftime)%14+9) +":00";
    html = '<p id="order-info" class="site-info '+fid+ftime+'" style="display: none;"><span class="text">'+date+' '+time+' '+fid+'号场地</span><i class="dcz-icon icon-close"></i>'
    html +='<input type="hidden" name="orders[]" value="' + fid + ':' + ftime + '" /></p>';
    $('.side-bar-input:eq(3)').append(html);
    $('.'+fid+ftime).fadeIn();
    $('.book .money-count').text(parseInt($('.book .money-count').text())+price)
    $('.'+fid+ftime).click(function(e){
        var target = e.srcElement ? e.srcElement : e.target;
        $('#'+fid+ftime).click();
         })
}

function select_toggle(e){
    var selected_icon = $('.selected-icon').attr('class').split(" ")[2];
    var is_select = new Array("dcz-icon icon-common-avaliable availiable","dcz-icon selected " + selected_icon);
    var target = e.srcElement ? e.srcElement : e.target;
    price = parseInt(target.attributes.price.value);
    ftime = target.attributes.ftime.value;
    fid = target.parentElement.parentElement.firstElementChild.attributes.fid.value; //获取FID
    sort = console.log(target.parentElement.parentElement.firstElementChild.attributes);
    if(target.attributes.class.value.split(" ")[2] == "availiable"){        
        //console.log(e.target.attributes.class.value.split(" ")[2]);  
        hide_price(e);    
        target.style.opacity=1;          //选中的透明度
        target.setAttribute('class',is_select[1]);
        addToCart(fid,ftime,price,sort);
        $('#totalField').text(parseInt($('#totalField').text())+1);
       
    }
    else
        if(target.attributes.class.value.split(" ")[1] == "selected"){
            $('.'+fid+ftime).fadeOut("fast");
            $('.'+fid+ftime).remove();
            $('.book .money-count').text(parseInt($('.book .money-count').text())-price)
            target.style.opacity=1         //未选的透明度
            target.setAttribute('class',is_select[0])
            $('#totalField').text(parseInt($('#totalField').text())-1);
            var html = "￥" + price ;
            target.innerHTML = html;
        }
         
}
function show_price(e){
     var target = e.srcElement ? e.srcElement : e.target;
     price = parseInt(target.attributes.price.value);
     //var html = "<div style='font-style:normal;line-height:34px;'>￥" + price + "</div>";
     var html = "￥" + price ;
     target.innerHTML = html;
}
function hide_price(e){
    var target = e.srcElement ? e.srcElement : e.target;
    target.innerHTML = '';
}
function select_date(date){
    $('#date').val(date);
    $(".loading").show();
    $(".sites").html("");
    $.ajaxSettings.async = false;
    html = "";
    $.getJSON('http://www.dingchangzi.net/buser.php/Api/getDateStatus?date=' + date,function(data){
        $(data).each(function(index){
            var obj = data[index];
            html +='<tr>';
            html +='<td  style="width:79px" class="field" sort="'+(index+1)+'" fid="'+obj[0].fid+'">'+(index+1)+'号场地</td>'; 
            delete obj.fid;         
            $(obj).each(function(index){
                html += '<td><i class="dcz-icon icon-common-selled ftime" ftime="'+ obj[index]['ftime'] +'"></i></td>';
            });
            html +="</tr>";
        })
        $(".sites").html(html);
    })
    iterates2(date);
    $('td .availiable').click(function(e){
        select_toggle(e);
        //hide_price(e);
    })
    /*$('td .availiable').mouseenter(function(e){
        show_price(e);
    })
    $('td .availiable').mouseleave(function(e){
        hide_price(e);
    })*/
    $(function(){
            $(".loading").hide();
    })
    console.log(date);
}
function clearCart(){
    $('p#order-info').remove();
    $('.money-count').text(0);
}
function change_sport(s){
    var s_icons = new Array("icon-badminton-selected","icon-football-selected","icon-tennis-selected","icon-basketball-selected","icon-pingpong-selected");
    $('.selected-icon').attr('class','dcz-icon selected-icon '+s_icons[parseInt(s)-1]);
    $('.dcz-icon.selected').click();
}
//初始加载
$(document).ready(function(){
    //iterates2();
    $('td .availiable').click(function(e){
        select_toggle(e);
        
    })
   /* $('td .availiable').mouseenter(function(e){
        show_price(e);
    })
    $('td .availiable').mouseleave(function(e){
        hide_price(e);
    })*/
    $('.time-item').children("div").click(function(e){
        $('.time-item').removeClass("active");
        $(this).parent().addClass("active");
        clearCart();
        select_date($(this).parent().attr("id"));
    })

    //setInterval("syncStatus()",9000);
})

