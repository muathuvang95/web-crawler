<?php

get_header('webcrl');

?>
<div id="webcrl-page">
	<?php if(is_user_logged_in() && current_user_can( 'manage_options' )) {
		$su = get_option( 'webcrl_su', '' );
		$dup = get_option( 'webcrl_dup', '' );
		$eup = get_option( 'webcrl_eup', '' );
		//print_r(parse_url(esc_url_raw(untrailingslashit('sdfasdf.com#998w7342'))));
	?>
	<div id="webcrl-step1">
		<p class="source-url">
			<label>Source URL:<br>
				<input type="text" id="webcrl-source-url" value="<?=esc_url($su)?>">
			</label>
		</p>
		<p class="detail-url-pattern">
			<label>Detail URL Pattern:<br>
				<input type="text" id="webcrl-detail-url-pattern" value="<?=esc_attr($dup)?>">
			</label>
		</p>
		<p class="exclude-url-pattern">
			<label>Exclude URL Pattern:<br>
				<input type="text" id="webcrl-exclude-url-pattern" value="<?=esc_attr($eup)?>">
			</label>
		</p>
		<button type="button" id="webcrl-scan" class="webcrl-button">Scan</button>
		<button type="button" id="webcrl-stop-scan" class="webcrl-button" disabled="disabled">Stop</button>
		<div class="webcrl-msg" id="webcrl-scan-message"></div>
	</div>
	<div class="webcrl-divi"></div>
	<div id="webcrl-step2">
		<p class="title-selector">
			<label>Title Selector:<br>
				<input type="text" id="webcrl-title-selector" value="">
			</label>
		</p>
		<p class="thumbnail-selector">
			<label>Thumbnail Selector:<br>
				<input type="text" id="webcrl-thumbnail-selector" value="">
			</label>
		</p>
		<p class="gallery-selector">
			<label>Gallery Selector:<br>
				<input type="text" id="webcrl-gallery-selector" value="">
			</label>
		</p>
		<p class="summary-selector">
			<label>Summary Selector:<br>
				<input type="text" id="webcrl-summary-selector" value="">
			</label>
		</p>
		<p class="content-selector">
			<label>Content Selector:<br>
				<input type="text" id="webcrl-content-selector" value="">
			</label>
		</p>
		<button type="button" id="webcrl-get-data" class="webcrl-button">Get data</button>
		<button type="button" id="webcrl-stop-get-data" class="webcrl-button">Stop</button>
		<div class="webcrl-msg" id="webcrl-get-data-message"></div>
	</div>
	<?php } else {
	?>
		Đăng nhập để sử dụng chức năng này
	<?php
	}?>
</div>
<?php

get_footer('webcrl');
