// showInfo.js
$(function(){
	// 点击弹出个人信息
    function showInfo (){
        $('#userInfoList').slideDown();
    };
    function hideInfo(){
        $('#userInfoList').slideUp();
    }
    var showFlag = false ; 
    $('#userName').on('click',function(){
        console.log(1);
        if (!showFlag) {
            showInfo();
            showFlag = true ;
        }else{
            hideInfo();
            showFlag = false ;
        }
    });
    console.log(showFlag);	
})
