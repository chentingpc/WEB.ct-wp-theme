
function resize_top(){
	var width = ( $(window).width() - $('#content').width() ) / 2 - 50;
	$('#toTop').css('right', width);
}

$(document).ready(function () {

	$("#submit-button").click(function(e){
		if ( is_logged_in == false ) {
			if ( $('input#author').val() == '' ) {
				alert('Please type yor name please.');
				return;
			}
			
			if ( $('input#email').val() == '' ) {
				alert('Please type your email, so we could be better in touch:)');
				return;
			} else if ( !$('input#email').val().match(/.+@.+\..+/) ) {
				alert('Please type a valid email address.');
				return;
			}
		}
		
		if ( $('textarea#comment').val() == '' ) {
			alert('Please type a comment.');
			return;
		}
		$('#commentform').submit();
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
	
	
	$(window).scroll(function(){
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
	
});