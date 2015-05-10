//设置场地详情页切换运动
function setSportsType(i,url){
    // 1 羽毛球 2足球 3网球
    html = '';
    var sports = new Array("badminton","football","tennis","basketball","pingpang");

    var sports_class = new Array("icon-badminton-white","icon-football-white","icon-tennis-white","icon-basketball-white","icon-pingpang-white");

    var s_icons = new Array("icon-badminton-","icon-football-","icon-tennis-","icon-basketball-","icon-pingpang-");

    var sname = new Array("羽毛球","足球","网球","篮球","乒乓球");


    html += '<i class="dcz-icon '+sports_class[parseInt(i)-1]+'"></i>';

    html += '<span class="text"><a href="'+url+'" style="text-decoration:none;color:white;">'+sname[parseInt(i)-1]+'</a></span>';

    $('#selected').attr('class','dcz-icon '+s_icons[parseInt(i)-1]+'yellow');
    $('#availiable').attr('class','dcz-icon '+s_icons[parseInt(i)-1]+'green');
    $('#selled').attr('class','dcz-icon '+s_icons[parseInt(i)-1]+'gray');

    $('.venues-more.venues-sports.left').append(html);

    //生成下拉列表
    var selectSporttArea = DropDownList.create({
        container :$('#selectSportArea'),
        attrs : {
            id : 'selectSportArea',   // 给dropdownlist一个id
            column :5,         // 展示5行
            width:177,         // 宽度为150px
            height: 49          // 每个option选项的高度
        },
        options : [
            [sname[parseInt(i)-1],sports[parseInt(i)-1],true],
            /*['足球','football'],
            ['网球','tennis'],
            ['篮球','basketball'],
            ['乒乓球','pingpang'],          
*/
           
        ]
    });
    selectSporttArea.change(function(){
        console.log(1);
        // alert(ddl_album.val());
    });  
}
//获取场次状态
function getStatus(fid,ftime){
    //var json = $.getJSON("{:U('getStatus')}");
    return $.getJSON('http://www.dingchangzi.net/index.php/Api/getStatus?fid='+fid+'&ftime='+ftime);
}
//设置场次状态
function setStatus(obj,status,price){

    var selected_icon = $('#selected').attr('class').split(" ")[1];

    var availiable_icon = $('#availiable').attr('class').split(" ")[1];

    var selled_icon = $('#selled').attr('class').split(" ")[1];

    var color =new Array("dcz-icon "+selled_icon,"dcz-icon "+availiable_icon+" availiable","dcz-icon "+selected_icon,"dcz-icon icon-badminton-blue");
    //0已订 1可预订 2已选 3仅学生
    obj.setAttribute('class',color[status]);
    if(status == 1){
        obj.setAttribute('price',price);
    }
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
            $.getJSON('http://www.dingchangzi.net/index.php/Api/getStatus?fid='+fid+'&ftime='+ftime,function(data){
                setStatus(obj,data.status,data.price);
            });            

        };
        
    };

}
//遍历设置场地状态(按场地)
function iterates2(date){
    var fields = $('.field');
    //var status = 
    var patt1=new RegExp("stid/(.*)+.+html+$");
    var stid = patt1.exec(document.location.href)[1];
    for (var i = fields.length - 1; i >= 0; i--) {
        var ftimes = $('.field:eq('+ i +')').siblings().find('i');
        fid = $('.field:eq('+ i +')').attr('fid');
        $.ajaxSettings.async = false;       //设置同步加载
        $.getJSON('http://www.dingchangzi.net/index.php/Api/getStatus2?fid='+fid+'&date='+date+'&stid='+ stid,function(data){
            for (var j = 0; j < ftimes.length; j++) {
                obj = ftimes[j];
                ftime = obj.getAttribute('ftime');
                obj.setAttribute('id',fid+ftime)
                setStatus(obj,data[j].status,data[j].price);
            }

        });
        
    }
}
//加入购物车
function addToCart(fid,ftime,price,sort){
        date = $('#usetime').val().split("/")[1] + "/" + $('#usetime').val().split("/")[2];
        time = (parseInt(ftime)%14+8)  + ":00-" + (parseInt(ftime)%14+9) +":00";
        html = '<p id="order-info" class="site-info '+fid+ftime+'" style="display: none;"><span class="text">'+date+' '+time+' '+fid+'号场地</span><i class="dcz-icon icon-close"></i>'
        html +='<input type="hidden" name="orders[]" value="' + fid + ':' + ftime + '" /></p>';
        $('.side-bar-input:eq(0)').append(html);
        $('.'+fid+ftime).fadeIn();
        $('.money-count').text(parseInt($('.money-count').text())+price);
        $('.'+fid+ftime).click(function(e){
            var target = e.srcElement ? e.srcElement : e.target;
            $('#'+fid+ftime).click();
             })
    
}
//清空购物车
function clearCart(){
    $('p#order-info').remove();
    $('#totalField').text(0);
    $('.money-count').text(0);
}

//场地图标点击
function select_toggle(e){

    hide_price(e);

    var selected_icon = $('#selected').attr('class').split(" ")[1];

    var availiable_icon = $('#availiable').attr('class').split(" ")[1];

    var selled_icon = $('#selled').attr('class').split(" ")[1];

    var is_select = new Array("dcz-icon "+availiable_icon+" availiable","dcz-icon "+selected_icon+" selected");
    var target = e.srcElement ? e.srcElement : e.target;
    price = parseInt(target.attributes.price.value);
    ftime = target.attributes.ftime.value;
    fid = target.parentElement.parentElement.firstElementChild.attributes.fid.value; //获取FID
    sort = console.log(target.parentElement.parentElement.firstElementChild.attributes);
    if(target.attributes.class.value.split(" ")[2] == "availiable"){        
        //console.log(e.target.attributes.class.value.split(" ")[2]);      
        target.style.opacity=1
        var orders = $('p#order-info');
            if(orders.length >= 5)
                alert("最多同时定5个");
            else{
                target.setAttribute('class',is_select[1])
                addToCart(fid,ftime,price,sort);
                $('#totalField').text(parseInt($('#totalField').text())+1);
            }
       
    }
    else
        if(target.attributes.class.value.split(" ")[2] == "selected"){
            $('.'+fid+ftime).fadeOut("fast");
            $('.'+fid+ftime).remove();
            $('.money-count').text(parseInt($('.money-count').text())-price)
            target.style.opacity=1
            target.setAttribute('class',is_select[0])
            $('#totalField').text(parseInt($('#totalField').text())-1);
        }
         
}
//图标hover显示价格
function show_price(e){
     var target = e.srcElement ? e.srcElement : e.target;
     price = parseInt(target.attributes.price.value);
     //var html = "<div class='price' style=\"color:#460;font-style:normal;background-image:url('/Public/Home/icons/price.png');background-repeat:no-repeat;line-height: 25px;margin-top: -15px;\">" + price + "元</div>";  // style=\"font-style:normal;line-height:55px;background-image:url('/Public/Home/icons/price.png');background-repeat:no-repeat;\"
     var html = + price + "元"; 
     target.innerHTML += html;
}
//隐藏价格
function hide_price(e){
    var target = e.srcElement ? e.srcElement : e.target;
    target.innerHTML = '';
    //$('.price').remove();
}
//切换时间
function select_date(date){
    var patt1=new RegExp("(.*?)+.+html+$","g");
    var stid = patt1.exec(document.location.href)[1];
    $(".loading").show();
    $(".sites").html("");
    $.ajaxSettings.async = false;
    html = "";
    $.getJSON('http://www.dingchangzi.net/index.php/Api/getDateStatus?date='+date+'&stid='+ stid,function(data){
        $(data).each(function(index){
            var obj = data[index];
            html +='<tr>';
            html +='<td  style="width:79px" class="field" sort="'+(index+1)+'" fid="'+obj[0].fid+'">'+(index+1)+'号场地</td>'; 
            delete obj.fid;         
            $(obj).each(function(index){
                html += '<td><i class="dcz-icon icon-badminton-gray ftime" ftime="'+ obj[index]['ftime'] +'"></i></td>';
            });
            html +="</tr>";
        })
        $(".sites").html(html);

    })
    iterates2(date);
    $('td .availiable').click(function(e){
        select_toggle(e);
    })
    $('td .availiable').mouseenter(function(e){
        show_price(e);
    })
    $('td .availiable').mouseleave(function(e){
        hide_price(e);
    })
    $(function(){
            $(".loading").hide();
    })
    $('#usetime').val(date);
    console.log(date);
}
//添加至收藏夹
function collect(stid){

    var url = "http://www.dingchangzi.net/index.php/User/addFavorites.html";

    data = {'stid':stid};
    
    $.post(url,data,function(res){
        if(res == "TRUE")
            alert("收藏成功");
    });
}
//初始加载
$(document).ready(function(){
    //iterates2();
    $('td .availiable').click(function(e){
        select_toggle(e);
    })
    $('td .availiable').mouseenter(function(e){
        show_price(e);
    })
    $('td .availiable').mouseleave(function(e){
        hide_price(e);
    })
    $('.time-item').children("div").click(function(e){
            $('.time-item').removeClass("active");
            $(this).parent().addClass("active");
            clearCart();
            select_date($(this).parent().attr("id"));
    })

})
