jQuery(function($){
				
	$('#menu li').hover(function(){
		$(this).addClass('menu-hover');
	},function(){
		$(this).removeClass('menu-hover');
	});

	$('.button').button();
});