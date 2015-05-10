// reserve-tab.js
$(function(){
	function sideTab(){
		//Default Action
	    $( ".tab-content" ).removeClass('active');  //Hide all content
	    $( "ul.tab-nav li:first" ).addClass( "active" ).show();  //Activate first tab
	    $( ".tab-content:first" ).addClass('active');  //Show first tab content
	     //On Click Event
	    $( "ul.tab-nav li" ).click( function () {
	    	var $navItems = $( "ul.tab-nav li" ),$tabContents = $('.tab-content'), m=1;
	        $navItems.removeClass( "active" );  //Remove any "active" class
	        for (var i = 0; i < $navItems.length; i++) {
	        	if($navItems[i]==this){
	        		m = i ;
	        	}
	        	console.log($navItems[i]);
	        }

	        $( this ).addClass( "active" );  //Add "active" class to selected tab
	        $( ".tab-content" ).removeClass('active');  //Hide all tab content
	        for (var j = 0; j < $tabContents.length; j++) {
	        	if(j == m ){
	        		$($tabContents[j]).addClass('active');
	        		return ;
	        	}
	        }
	         return   false ;
	    });
	}
	sideTab();


	// vip or novip
	$('#isvip-2').on('click',function(){
		$('#novip-content-2').slideUp();
		$('#isvip-content-2').slideDown();
	});
	$('#novip-2').on('click',function(){
		$('#isvip-content-2').slideUp();
		$('#novip-content-2').slideDown();
	});
	$('#isvip-1').on('click',function(){
		$('#novip-content-1').slideUp();
		$('#isvip-content-1').slideDown();
	});
	$('#novip-1').on('click',function(){
		$('#isvip-content-1').slideUp();
		$('#novip-content-1').slideDown();
	});

})