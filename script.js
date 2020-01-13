jQuery(function($){
	var ajax_scan = [];
	function arrUnique(value, index, self) { 
	    return self.indexOf(value) === index;
	}

	function web_crawl(source_url, detail_url_pattern, exclude_url_pattern='') {
		var msg = $('#webcrl-scan-message');
		//console.log(source_url);
		if(source_url.length>0 && detail_url_pattern!='') {
			var url = source_url.pop();
			var ajax = $.ajax({
				url: webcrl.ajax_url+'?action=webcrl_scan',
				type: 'POST',
				data: {su:url,dup:detail_url_pattern,eup:exclude_url_pattern},
				dataType: 'json',
				beforeSend: function(xhr) {
					msg.prepend('<li>'+url+'</li>');
				},
				success: function(res) {
					if(res['error']!='') {
						msg.prepend('<li>'+res['error']+'</li>');
					}
					console.log(res['links']);
					var next_crawl = source_url.concat(res['next_crawl']);
					next_crawl = next_crawl.filter(arrUnique);

					web_crawl(next_crawl, detail_url_pattern, exclude_url_pattern);
				}
			});
			ajax_scan.push(ajax);
		} else {
			msg.prepend('<li>Complete!</li>');
			$('#webcrl-scan').prop('disabled', false);
			$('#webcrl-stop-scan').prop('disabled', true);
		}
	}

	$('#webcrl-scan').on('click', function(e){
		var $this = $(this),
			su = [$('#webcrl-source-url').val()],
			dup = $('#webcrl-detail-url-pattern').val(),
			eup = $('#webcrl-exclude-url-pattern').val(),
			msg = $('#webcrl-scan-message');

		msg.html('');
		if( su[0]!='' && dup!='' ) {
			$this.prop('disabled', true);
			$('#webcrl-stop-scan').prop('disabled', false);

			web_crawl(su, dup, eup);
		} else {
			msg.html('Nhập đầy đủ thông tin cần quét!');
		}	
	});

	$('#webcrl-stop-scan').on('click', function(e){
		if(ajax_scan.length>0) {
			$.each(ajax_scan, function(index, value){
				if(value!=null) {
					value.abort();
				}
			});
		}
		$(this).prop('disabled', true);
		$('#webcrl-scan').prop('disabled', false);
	});
});