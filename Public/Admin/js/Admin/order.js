//购买和锁定的 

function buy(type=0){
    var url = "http://www.dingchangzi.net/buser.php/Api/createOrder/status/"+type+".html";

    data = $('.book').serialize();
    
    $.post(url,data,function(res){
        if(res == "true")
            alert("成功");
        else
            alert("失败");
    });
}
