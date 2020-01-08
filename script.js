jQuery(function($){
	$('#webcrl-scan').on('click', function(e){
		var $this = $(this),
			su = $('#webcrl-source-url').val(),
			dup = $('#webcrl-detail-url-pattern').val(),
			msg = $('#webcrl-scan-message');
		if(su!='' && dup!='') {
			$.ajax({
				url: webcrl.ajax_url+'?action=webcrl_scan',
				type: 'POST',
				data: {su:su,dup:dup},
				beforeSend: function(xhr) {
					msg.html('Scanning...');
					$this.prop('disabled', true);
					$('#webcrl-stop-scan').prop('disabled', false);
				},
				success: function(res) {
					$this.prop('disabled', false);
					$('#webcrl-stop-scan').prop('disabled', true);
					msg.html(res);
				}
			});
		} else {
			msg.html('Nhập đầy đủ thông tin cần thiết!');
			$('#webcrl-source-url').focus();
		}
	});
});