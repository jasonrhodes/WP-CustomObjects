jQuery(document).ready( function ($) {

	$('.upload-image-button').click( function () {
		
		var myinput = $(this).parent().find('.upload-input');
		myinput.addClass('waiting-for-value');
		formfield = myinput.attr('name');
		tb_show( '', 'media-upload.php?type=file&amp;TB_iframe=true' );
		return false;
		
	});
	
	window.send_to_editor = function (html) {
		$('.waiting-for-value').val('Testing my plugin');
	}
	
});