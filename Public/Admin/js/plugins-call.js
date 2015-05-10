// plugins-call.js
$(document).ready(function(){

    // palceholder插件调用
    $('.placeholder').placeholder({
        placeholderColor:'#FFF',
        isUseSpan:false, //是否使用插入span标签模拟placeholder的方式,默认false,默认使用value模拟
        onInput:true
    });


    // 日历插件调用
    $(".datepicker").datepicker({
        dateFormat: "yyyy-m-d"
    });
    $(".datepicker-days").click(function(){
        $(".datepicker-container").hide();
    })

    // droplist 插件调用
    /*var selectVenues = DropDownList.create({
        container :$('#selectVenues'),
        attrs : {
            id : 'selectVenues',   // 给dropdownlist一个id
            column :20,         // 展示4行
            width:200,         // 宽度为150px
            height: 40          // 每个option选项的高度
        },
        options : [
            ['羽毛球','001'],
            ['乒乓球','002'],
            ['网球','003',true],
            ['台球','004'],
        ]
    });
    selectVenues.change(function(){
        // alert(ddl_album.val());
    });

    var selectSport = DropDownList.create({
        container :$('#selectSport'),
        attrs : {
            id : 'selectSport',   // 给dropdownlist一个id
            column :20,         // 展示4行
            width:200,         // 宽度为150px
            height: 40          // 每个option选项的高度
        },
        options : [
            ['默认相册','001'],
            ['我的收藏','002'],
            ['大学同学','003',true],
            ['亲朋好友','004'],
            ['明星们','005'],
            ['狗仔队','006'],
        ]
    });
    selectSport.change(function(){
        // alert(ddl_album.val());
    });*/

})