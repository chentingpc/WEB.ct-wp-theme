
var loading_tick;
	
function dynamic_loading(){
	loading_tick += 1;
	if ( loading_tick > 6 ) {
		$('.loading').html('Loading&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
		loading_tick = 0;
	} else {
		switch (loading_tick)
		{
		case 1:
			$('.loading').html('Loading.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
			break;
		case 2:
			$('.loading').html('Loading..&nbsp;&nbsp;&nbsp;&nbsp;');
			break;
		case 3:
			$('.loading').html('Loading...&nbsp;&nbsp;&nbsp;');
			break;
		case 4:
			$('.loading').html('Loading....&nbsp;&nbsp;');
			break;
		case 5:
			$('.loading').html('Loading.....&nbsp;');
			break;
		default:
			break;
		
		}
	}
	
	if ( loading_tick >= 0 )
		setTimeout(function(){dynamic_loading()},300);
}

function get_more_items(){
	if ( next_page_num <= 1 || url.indexOf('view=cat') != -1 )
		return;
		
	$('#content').append('<div class="loading">Loading&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>');
	loading_tick = 0;
	dynamic_loading();
	
	url = url.replace(/\/page\/\d+/, '/\/page/'+next_page_num.toString());
	urls = url.split('?');
		
	if ( urls.length != 1 ) {
		urls_end = urls[1].split('#');
		if ( url[0].indexOf('/page/') == -1 )
			new_url = urls[0] + '/page/' + next_page_num + '/?' + urls_end[0] + '&ajax=1';
		else
			new_url = url[0] + '?' + urls_end[0] + '&ajax=1';
	}
	else
		if ( url[0].indexOf('/page/') == -1 )
			new_url = url + '/page/' + next_page_num + '/?ajax=1';
		else
			new_url = url + '/?ajax=1';

	$.ajax({
		url:new_url,
		success:function(result){
			loading_tick = -1;
			$('.loading').remove();
			if ( result.trim().length == 0 ){
					$('#content').append('<div class="loading_end">The End.</div>');
				return;
			}
			next_page_num += 1;
			result = '<p>'+result+'</p>';
			$(result).appendTo('#content');
			get_more_avaiable = true;
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			loading_tick = -1;
			$('.loading').remove();
			if ( XMLHttpRequest.status == 404 ){
				$('#content').append('<div class="loading_end">The End.</div>');
			} else {
				$('#content').append('<div class="loading_end">Error Occured, code: ' + XMLHttpRequest.status + '.</div>');
			}
			return;
		}
	});
}

function resize_top(){
	var width = ( $(window).width() - $('#content').width() ) / 2 - 50;
	$('#toTop').css('right', width);
}

$(document).ready(function () { 
	
	$(window).scroll(function(){
		if ( $(window).scrollTop() + $(window).height() == $(document).height() 
			&& $('#content').height() > $(window).height() ){
			if ( get_more_avaiable == true ) {
				get_more_avaiable = false;
				get_more_items();
			}
		}
		
		if ( $(window).scrollTop() > 0 ){
			$('#toTop').fadeIn(300);
		} else {
			$('#toTop').fadeOut(300);
		}
	});
	
	$(window).resize(function(){
		resize_top();
	})
	
	resize_top();
	$('#toTop').hide();
	$('#toTop').click(function(){
		$('body,html').animate({ scrollTop: 0 }, 600);
		return false;
	});
	
	var handle = null;
	$('#categoryform .children .children').hide();
	$('#categoryform .children').hover(function(){
		var that = this;
		handle = setTimeout(function(){
			$(that).children().children().next().fadeIn(400);
		}, 300);
	}, function(){
		clearTimeout(handle);
		$(this).children().children().next().fadeOut(400);
	})
	
});