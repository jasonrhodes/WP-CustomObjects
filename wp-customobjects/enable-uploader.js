jQuery(document).ready( function ($) {

	$('.upload-button').click( function () {
		
		var myinput = $(this).parent().find('.upload-input');
		myinput.addClass('waiting-for-value');
		formfield = myinput.attr('name');
		tb_show( '', 'media-upload.php?TB_iframe=true' );
		return false;
		
	});
	
	window.send_to_editor = function (html) {
		$('.waiting-for-value').val('Testing my plugin').removeClass('waiting-for-value');
		tb_remove();
	}
	
});