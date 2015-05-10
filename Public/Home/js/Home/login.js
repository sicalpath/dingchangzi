//login

function login(reurl){
	var errmsg = new Array('手机号或密码不能为空' , '手机号码不正确' , '账号或密码不正确' );
    var url = "http://www.dingchangzi.net/index.php/Login/login.html";


    data = {phone:$('#phone').val(),password:$('#password').val(),verify:$('#verify').val()};
    
    $.post(url,data,function(res){
        if(res == "0")
            window.location = reurl;
        else
            alert(errmsg[parseInt(res)-1]);
    });
}