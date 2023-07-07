(function($) {
	$(function() {
		new FLBuilderCountdown({
			id: '<?php echo $id; ?>',
			time: '<?php echo $module->get_time(); ?>',
			type: '<?php echo $settings->layout; ?>',
			redirect: '<?php echo $settings->redirect_when; ?>',
			redirect_url: '<?php echo $settings->redirect_url; ?>',
		});
	});
})(jQuery);
