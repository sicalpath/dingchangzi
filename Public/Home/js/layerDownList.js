// layerDownList.js
$(function(){
	var citySelectArea = DropDownList.create({
			container :$('#citySelectArea'),
			attrs : {
			id : 'citySelectArea',   // 给dropdownlist一个id
			column :6,         // 展示6行
			width:100,         // 宽度为150px
			height: 30          // 每个option选项的高度
		},
		options : [
			['天津市','000',true],
			['北京市','001']
		]
	});


	var schoolSelectArea = DropDownList.create({
			container :$('#schoolSelectArea'),
			attrs : {
			id : 'schoolSelectArea',   // 给dropdownlist一个id
			column :6,         // 展示6行
			width:140,         // 宽度为150px
			height: 30          // 每个option选项的高度
		},
		options : [
			['天津大学','000',true],
			['南开大学','001']
		]
	});
})