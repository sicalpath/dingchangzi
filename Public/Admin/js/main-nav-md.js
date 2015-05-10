// main-nav-md.js
$(function(){
	(function mainNavMd(){
			var mainNav = $('#mainNav .nav-item') ;
			var navActiveBar = $('#navActiveBar') ;
			var navActiveBarL = navActiveBar[0].offsetLeft;
			var isClick = false ;
			mainNav.on('click',function(e){
				var _this = $(this) ;
				navActiveBar.animate({left:_this[0].offsetLeft+"px"},100);
			})
		})();
	// mainNavMd();



})