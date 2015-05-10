// plugins-call.js
$(function(){
    

	// 日历插件调用
	$(".datepicker").datepicker({
		dateFormat: "yyyy-m-d",
        onSelect: function(dateText, inst){
            $(".datepicker-container").hide();
        }
	});
    $(".datepicker-days").click(function(){
        $(".datepicker-container").hide();
    })
	// 下拉列表插件调用
	// 通过JSON数据创建自定义下拉框
	// 选择区域
    var filterSelectArea = DropDownList.create({
        container :$('#filterSelectArea'),
        attrs : {
            id : 'filterSelectArea',   // 给dropdownlist一个id
            column :20,         // 展示4行
            width:150,         // 宽度为150px
            height: 40          // 每个option选项的高度
        },
        options : [
            ['全部','000',true],
            ['和平区','001'],
            ['河东区','002'],
            ['河西区','003'],
            ['南开区','004'],
            ['河北区','005'],
            ['红桥区','006'],
            ['塘沽区','007'],
            ['汉沽区','008'],
            ['大港区','009'],
            ['东丽区','010'],
            ['西青区','011'],
        ]
    });
	filterSelectArea.change(function(){
		// alert(ddl_album.val());
	});

	// 选择特色
	var filterSelectChar = DropDownList.create({
        container :$('#filterSelectChar'),
        attrs : {
            id : 'filterSelectChar',   // 给dropdownlist一个id
            column :20,         // 展示4行
            width:150,         // 宽度为150px
            height: 40          // 每个option选项的高度
        },
        options : [
            ['请选择','001',true],
            ['安静','002'],
            ['整洁','003'],
            ['停车','004'],
            ['洗浴','005'],
        ]
    });
	filterSelectChar.change(function(){
		// alert(ddl_album.val());
	});


    // 选择时段
    var filterSelectTime = DropDownList.create({
        container :$('#filterSelectTime'),
        attrs : {
            id : 'filterSelectTime',   // 给dropdownlist一个id
            column :20,         // 展示4行
            width:150,         // 宽度为150px
            height: 40          // 每个option选项的高度
        },
        checkboxes:true,
        options : [
            ['任意','000',true],
            ['8:00-9:00','001'],
            ['9:00-9:00','002'],
            ['10:00-11:00','003'],
            ['11:00-12:00','004'],
            ['12:00-13:00','005'],
            ['13:00-14:00','006'],
            ['14:00-15:00','007'],
            ['15:00-16:00','008'],
            ['16:00-17:00','009'],
            ['17:00-18:00','010'],
            ['18:00-19:00','011'],
            ['19:00-20:00','012'],
            ['20:00-21:00','013'],
            ['21:00-22:00','014'],
        ]
    });
    filterSelectArea.change(function(){
        // alert(ddl_album.val());
    });

})