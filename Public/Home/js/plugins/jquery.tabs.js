/* =================================================
// jQuery Tabs Plugins 1.3
// author : chenmnkken@gmail.com
// Url: http://stylechen.com/jquery-tabs.html
// Data : 2012-09-06
// =================================================*/

;(function($){
	$.fn.Tabs = function(options){
		return this.each(function(){
			// 处理参数
			options = $.extend({
				event : 'mouseover',
				timeout : 0,
				auto : 0,
				callback : null,
				switchBtn : false
			}, options);
			
			var self = $(this),
				tabBox = self.children( 'div.tab_box' ).children( 'div' ),
				menu = self.children( 'ul.tab_menu' ),
				items = menu.find( 'li' ),
				timer;
					
			var tabHandle = function( elem ){
					elem.siblings( 'li' )
						.removeClass( 'current' )
						.end()
						.addClass( 'current' );
						
					tabBox.siblings( 'div' )
						.addClass( 'hide' )
						.end()
						.eq( elem.index() )
						.removeClass( 'hide' );
				},
					
				delay = function( elem, time ){
					time ? setTimeout(function(){ tabHandle( elem ); }, time) : tabHandle( elem );
				},
				
				start = function(){
					if( !options.auto ) return;
					timer = setInterval( autoRun, options.auto );
				},
				
				autoRun = function( isPrev ){
					var current = menu.find( 'li.current' ),
						firstItem = items.eq(0),
						lastItem = items.eq(items.length - 1),
						len = items.length,
						index = current.index(),
						item, i;
					
					if( isPrev ){
						index -= 1;
						item = index === -1 ? lastItem : current.prev( 'li' );
					}
					else{
						index += 1;
						item = index === len ? firstItem : current.next( 'li' );
					}
					
					i = index === len ? 0 : index;
					
					current.removeClass( 'current' );
					item.addClass( 'current' );
					
					tabBox.siblings( 'div' )
						.addClass( 'hide' )
						.end()
						.eq(i)
						.removeClass( 'hide' );
						
					if( options.callback ){
						options.callback.call( self );
					}
				};
		
			items.bind( options.event, function(){
				delay( $(this), options.timeout );
				if( options.callback ){
					options.callback.call( self );
				}
			});
			
			if( options.auto ){
				start();
				self.hover(function(){
					clearInterval( timer );
					timer = undefined;
				},function(){
					start();
				});
			}
			
			if( options.switchBtn ){
				self.append( '<a href="#prev" class="tab_prev">previous</a><a href="#next" class="tab_next">next</a>' );
				var prevBtn = $( '.tab_prev', self ),
					nextBtn = $( '.tab_next', self );
				
				prevBtn.click(function( e ){
					autoRun( true );
					e.preventDefault();
				});
				
				nextBtn.click(function( e ){
					autoRun();
					e.preventDefault();
				});
			}
			
		});
	};
})(jQuery);