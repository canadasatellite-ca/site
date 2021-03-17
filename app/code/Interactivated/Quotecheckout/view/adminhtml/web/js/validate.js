require([
	'jquery'
], function($) {
	var tmp = 0;
	$(function(){	
		$('#onestepcheckout_display_setting_style_color, #onestepcheckout_display_setting_checkout_button_color').ColorPicker({
			onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
			},
			onBeforeShow: function () {
				$(this).ColorPickerSetColor(this.value);
			}
		})
		.bind('keyup', function(){
			$(this).ColorPickerSetColor(this.value);
		});
	});
})
